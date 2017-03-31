<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DebitNote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\DebitNoteAccountMovement;
use AppBundle\Entity\AccountMovement;
use AppBundle\Entity\PurchaseOrder;
use AppBundle\Entity\DebitNoteItem;

/**
 * Debitnote controller.
 *
 * @Route("/admin/debitnote")
 */
class DebitNoteController extends Controller {

    /**
     * Lists all debitNote entities.
     *
     * @Route("/", name="debitnote_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $datatable = $this->get('app.datatable.debitnote');
        $datatable->buildDatatable();

        return $this->render('debitnote/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }
    
    /**
     * @Route("/results", name="debitnote_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.debitnote');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new debitNote entity.
     *
     * @Route("/new", name="debitnote_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
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
                $debitNote = DebitNote::createFromOrder($purchaseOrder, $salesCondition);
                
                foreach($purchaseOrder->getOrderItems() as $item){

                    //crea y guarda item
                    $debitNoteItem = DebitNoteItem::createForDebitNoteFromOrderItem($debitNote, $item);
                    $em->persist($debitNoteItem);
                }
                
                // Si la condicion de venta es CTA CTE, genera el movimiento en la cuenta del cliente
                if($salesCondition->getId() == 2){

                    $detail = AccountMovement::DEBITNOTE_MOVEMENT;
                    $amount = $debitNote->getTotal();
                    $account = $debitNote->getCustomer()->getAccount();

                    $movement = new DebitNoteAccountMovement();
                    $movement->generateAccountMovementForAccount($detail, $amount, $account, $debitNote);

                    $account->setBalance($account->getBalance() + $amount);

                    $em->persist($account);
                    $em->persist($movement);
                    $debitNote->setTotalPayed(0);
                }else{
                    $debitNote->setTotalPayed($debitNote->getTotal());
                }
                
                $em->persist($debitNote);

                $em->flush();
                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()->add(
                        'success', 'Los cambios fueron guardados correctamente'
                );

                return $this->redirectToRoute('debitnote_show', array('id' => $debitNote->getId()));
                    
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

        return $this->render('debitnote/new.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $form->createView(),
            'isNew' => 1,
            'salesConditions' => $salesConditions,
            'salesConditionSelected' => $salesConditionSelected,
        ));
    }

    /**
     * Finds and displays a debitNote entity.
     *
     * @Route("/{id}", name="debitnote_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(DebitNote $debitNote) {

        return $this->render('debitnote/show.html.twig', array(
                    'debitNote' => $debitNote,
        ));
    }

}
