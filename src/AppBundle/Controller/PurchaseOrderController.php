<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PurchaseOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceItem;
use AppBundle\Entity\SalesCondition;
use AppBundle\Entity\InvoiceAccountMovement;

/**
 * Purchaseorder controller.
 *
 * @Route("/admin/purchaseorder")
 */
class PurchaseOrderController extends Controller
{
    /**
     * Lists all purchaseOrder entities.
     *
     * @Route("/", name="purchaseorder_index")
     * @Method("GET")
     */
    public function indexAction() {
        $datatable = $this->get('app.datatable.purchaseorder');
        $datatable->buildDatatable();

        return $this->render('purchaseorder/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/results", name="purchaseorder_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.purchaseorder');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new purchaseOrder entity.
     *
     * @Route("/new", name="purchaseorder_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $purchaseOrder = new Purchaseorder();
        $purchaseOrder->setDiscountAmount(0);
        $purchaseOrder->setShippingAmount(0);
        $form = $this->createForm('AppBundle\Form\PurchaseOrderType', $purchaseOrder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            
            try {
                $em->persist($purchaseOrder);
                
                foreach($purchaseOrder->getOrderItems() as $item){

                    //descuenta stock
                    $product = $em->getRepository('AppBundle:Product')->findByCode($item->getProductCode());
                    if($product){
                        $product->setStock($product->getStock() - $item->getProductQuantity());
                        $em->persist($product);
                    }
                    
                    //guarda item
                    $item->setOrder($purchaseOrder);
                    $em->persist($item);
                }

                $em->flush();
                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()->add(
                        'success', 'Los cambios fueron guardados correctamente'
                );

                return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
                    
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                
                $this->get('session')->getFlashBag()->add(
                    'danger', 'Se produjeron errores al intentar guardar los cambios. ' . $e->getMessage()
                );
            }
        }
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->get('session')->getFlashBag()->add(
                    'danger', 'Se produjeron errores al intentar guardar los cambios.'
            );
        }

        return $this->render('purchaseorder/new_edit.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $form->createView(),
            'isNew' => 1,
        ));
    }

    /**
     * Finds and displays a purchaseOrder entity.
     *
     * @Route("/{id}", name="purchaseorder_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(PurchaseOrder $purchaseOrder)
    {
        $deleteForm = $this->createDeleteForm($purchaseOrder);
        
        if($purchaseOrder->getCustomer()->getAccount()){
            $salesConditions = $this->getDoctrine()->getRepository('AppBundle:SalesCondition')->findAll();
        }else{
            $salesConditions = $this->getDoctrine()->getRepository('AppBundle:SalesCondition')->findBy(['id' => 1]);
        }

        return $this->render('purchaseorder/show.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'delete_form' => $deleteForm->createView(),
            'salesConditions' => $salesConditions,
        ));
    }

    /**
     * Displays a form to edit an existing purchaseOrder entity.
     *
     * @Route("/{id}/edit", name="purchaseorder_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PurchaseOrder $purchaseOrder)
    {
        $deleteForm = $this->createDeleteForm($purchaseOrder);
        $editForm = $this->createForm('AppBundle\Form\PurchaseOrderType', $purchaseOrder);
        $editForm->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        $oldPurchaseOrder = $em
                      ->getUnitOfWork()
                      ->getOriginalEntityData($purchaseOrder);
        
        if($oldPurchaseOrder['orderState']->getId() != 1){
            $this->get('session')->getFlashBag()->add(
                        'danger', 'Únicamente pueden editarse pedidos en estado ABIERTO'
                );

                return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
            
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            
            try {
                
                //devuelve stock pedido previo
                $previousItems = $em->getRepository('AppBundle:OrderItem')->findByOrderId($purchaseOrder->getId());
                foreach($previousItems as $item){
                    $oldItem = $em
                      ->getUnitOfWork()
                      ->getOriginalEntityData($item);
                    
                    $product = $em->getRepository('AppBundle:Product')->findByCode($oldItem['productCode']);
                    if($product){
                        $product->setStock($product->getStock() + $oldItem['productQuantity']);
                        $em->persist($product);
                    }
                }

                foreach($purchaseOrder->getOrderItems() as $item){
                    //descuenta nuevamente stock
                    $product = $em->getRepository('AppBundle:Product')->findByCode($item->getProductCode());
                    if($product){
                        $product->setStock($product->getStock() - $item->getProductQuantity());
                        $em->persist($product);
                    }
                    
                    //guarda item
                    $item->setOrder($purchaseOrder);
                    $em->persist($item);
                }

                $em->flush();
                
                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()->add(
                        'success', 'Los cambios fueron guardados correctamente'
                );

                return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
            
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                
                $this->get('session')->getFlashBag()->add(
                    'danger', 'Se produjeron errores al intentar guardar los cambios. ' . $e->getMessage()
                );
            }
        }
        
        if ($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->get('session')->getFlashBag()->add(
                    'danger', 'Se produjeron errores al intentar guardar los cambios.'
            );
        }

        return $this->render('purchaseorder/new_edit.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $editForm->createView(),
            'isNew' => 0,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Generates a invoice for an existing purchaseOrder entity.
     *
     * @Route("/{id}/generateinvoice/{salesConditionId}", name="purchaseorder_generate_invoice", options={"expose"=true})
     * @ParamConverter("salesCondition", options={"id" = "salesConditionId"})
     * @Method({"GET", "POST"})
     */
    public function invoiceOrderAction(Request $request, PurchaseOrder $purchaseOrder, SalesCondition $salesCondition)
    {
        $em = $this->getDoctrine()->getManager();
        //si ya fue facturado
        if($em->getRepository('AppBundle:Invoice')->findByOrderId($purchaseOrder->getId())){
            $this->get('session')->getFlashBag()->add(
                'danger', 'Ya existe una factura para este pedido. La misma no pudo ser generada'
            );
            
            return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
        }
        
        //si el estado no es abierto
        if($purchaseOrder->getOrderState()->getId() != 1){
            $this->get('session')->getFlashBag()->add(
                'danger', 'Error al generar la factura, únicamente se pueden facturar pedidos en estado ABIERTO.'
            );
            
            return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
            
        }
        
        $em->getConnection()->beginTransaction();
        try {

            //crea la factura para la dicha orden
            $invoice = Invoice::createFromOrder($purchaseOrder, $salesCondition);
            $em->persist($invoice);
            
            //busca el estado cerrado y lo asigna
            $orderState = $this->getDoctrine()->getRepository('AppBundle:OrderState')->find(2);
            $purchaseOrder->setOrderState($orderState);
            $em->persist($purchaseOrder);

            //genera los items de factura
            foreach($purchaseOrder->getOrderItems() as $orderItem){
                $invoiceItem = InvoiceItem::createForInvoiceFromOrderItem($invoice, $orderItem);
                $em->persist($invoiceItem);
            }

            // Si la condicion de venta es CTA CTE, genera el movimiento en la cuenta del cliente
            if($salesCondition->getId() == 2){

                $detail = 'Factura';
                $amount = $invoice->getTotal();
                $account = $invoice->getCustomer()->getAccount();
                
                $movement = new InvoiceAccountMovement();
                $movement->generateAccountMovementForAccount($detail, $amount, $account);
                $movement->setInvoice($invoice);
                
                $account->setBalance($account->getBalance() - $amount);
                
                $em->persist($account);
                $em->persist($movement);
            }
            
            $em->flush();
            $em->getConnection()->commit();

            $this->get('session')->getFlashBag()->add(
                    'success', 'La factura fue generada correctamente'
            );
            
            return $this->redirectToRoute('invoice_show', array('id' => $invoice->getId()));

        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            $this->get('session')->getFlashBag()->add(
                'danger', 'Se produjeron errores al intentar generar la factura. ' . $e->getMessage()
            );
        }
                
    }

    /**
     * Deletes a purchaseOrder entity.
     *
     * @Route("/{id}", name="purchaseorder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PurchaseOrder $purchaseOrder)
    {
        $form = $this->createDeleteForm($purchaseOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            
            try {
                
                //devuelve stock pedido previo
                $previousItems = $em->getRepository('AppBundle:OrderItem')->findByOrderId($purchaseOrder->getId());
                foreach($previousItems as $item){
                    $oldItem = $em
                      ->getUnitOfWork()
                      ->getOriginalEntityData($item);
                    
                    $product = $em->getRepository('AppBundle:Product')->findByCode($oldItem['productCode']);
                    if($product){
                        $product->setStock($product->getStock() + $oldItem['productQuantity']);
                        $em->persist($product);
                    }
                }
                
                $em->remove($purchaseOrder);
                
                $em->flush();
                
                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()->add(
                        'success', 'El pedido se borró correctamente. Se devolvió el stock correctamente'
                );

                
            
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                
                $this->get('session')->getFlashBag()->add(
                    'danger', 'Se produjeron errores al borrar el elemento. ' . $e->getMessage()
                );
            }
        }
        
        return $this->redirectToRoute('purchaseorder_index');

        
    }

    /**
     * Creates a form to delete a purchaseOrder entity.
     *
     * @param PurchaseOrder $purchaseOrder The purchaseOrder entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PurchaseOrder $purchaseOrder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('purchaseorder_delete', array('id' => $purchaseOrder->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
