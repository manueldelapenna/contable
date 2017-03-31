<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CreditNote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\PurchaseOrder;
use AppBundle\Entity\SalesCondition;
use AppBundle\Entity\CreditNoteItem;
use AppBundle\Entity\CreditNoteAccountMovement;
use AppBundle\Entity\AccountMovement;
use Symfony\Component\Config\Definition\Exception\Exception;
/**
 * Creditnote controller.
 *
 * @Route("/admin/creditnote")
 */
class CreditNoteController extends Controller
{
    /**
     * Lists all creditNote entities.
     *
     * @Route("/", name="creditnote_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $datatable = $this->get('app.datatable.creditnote');
        $datatable->buildDatatable();

        return $this->render('creditnote/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }
    
    /**
     * @Route("/results", name="creditnote_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.creditnote');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new creditNote entity.
     *
     * @Route("/new", name="creditnote_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $orderState = $em->getRepository('AppBundle:OrderState')->find(1);
        
        $salesConditions = $this->getDoctrine()->getRepository('AppBundle:SalesCondition')->findAll();
        
        $purchaseOrder = new Purchaseorder();
        $purchaseOrder->setDiscountAmount(0);
        $purchaseOrder->setShippingAmount(0);
        
        $purchaseOrder->setOrderState($orderState);
        $form = $this->createForm('AppBundle\Form\PurchaseOrderType', $purchaseOrder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            //levanta condicion de venta
            $salesCondition = $this->getDoctrine()->getRepository('AppBundle:SalesCondition')->find($request->request->get('sales-condition-id'));
            $em->getConnection()->beginTransaction();
            
            try {
                //si la condicion es CTA CTE y el cliente no tiene cuenta, tira error
                if(($salesCondition->getId() == 2) && is_null($purchaseOrder->getCustomer()->getAccount())){
                    throw new Exception('El cliente seleccionado no posee CTA CTE. Cree una cuenta para dicho usuario o cambie la condición de venta.');
                }
                $creditNote = CreditNote::createFromOrder($purchaseOrder, $salesCondition);
                
                foreach($purchaseOrder->getOrderItems() as $item){

                    //devuelve stock
                    $product = $em->getRepository('AppBundle:Product')->findByCode($item->getProductCode());
                    if($product){
                        $product->setStock($product->getStock() + $item->getProductQuantity());
                        $em->persist($product);
                    }
                    
                    //crea y guarda item
                    $creditNoteItem = CreditNoteItem::createForCreditNoteFromOrderItem($creditNote, $item);
                    $em->persist($creditNoteItem);
                }
                
                // Si la condicion de venta es CTA CTE, genera el movimiento en la cuenta del cliente
                if($salesCondition->getId() == 2){

                    $detail = AccountMovement::CREDITNOTE_MOVEMENT;
                    $amount = $creditNote->getTotal();
                    $account = $creditNote->getCustomer()->getAccount();

                    $movement = new CreditNoteAccountMovement();
                    $movement->generateAccountMovementForAccount($detail, $amount, $account, $creditNote);

                    $account->setBalance($account->getBalance() - $amount);

                    $em->persist($account);
                    $em->persist($movement);
                    $creditNote->setTotalDiscounted(0);
                }else{
                    $creditNote->setTotalDiscounted($creditNote->getTotal());
                }
                
                $em->persist($creditNote);

                $em->flush();
                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()->add(
                        'success', 'Los cambios fueron guardados correctamente'
                );

                return $this->redirectToRoute('creditnote_show', array('id' => $creditNote->getId()));
                    
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
        
        $salesConditionSelected = $request->request->get('sales-condition-id');

        return $this->render('creditnote/new.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $form->createView(),
            'isNew' => 1,
            'salesConditions' => $salesConditions,
            'salesConditionSelected' => $salesConditionSelected,
        ));
    }

    /**
     * Finds and displays a creditNote entity.
     *
     * @Route("/{id}", name="creditnote_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(CreditNote $creditNote)
    {
        return $this->render('creditnote/show.html.twig', array(
            'creditNote' => $creditNote,
            
        ));
    }
}
