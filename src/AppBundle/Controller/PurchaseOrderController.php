<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PurchaseOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Repository\ProductRepository;

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

        return $this->render('purchaseorder/show.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'delete_form' => $deleteForm->createView(),
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

                return $this->redirectToRoute('purchaseorder_edit', array('id' => $purchaseOrder->getId()));
            
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
