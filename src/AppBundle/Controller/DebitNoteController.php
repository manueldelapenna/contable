<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DebitNote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\DebitNoteAccountMovement;
use AppBundle\Entity\AccountMovement;

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
        $debitNote = new Debitnote();
        $form = $this->createForm('AppBundle\Form\DebitNoteType', $debitNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();

            try {
                if(($debitNote->getSalesCondition()->getId() == 2) && is_null($debitNote->getCustomer()->getAccount())) {
                    throw new Exception('El cliente seleccionado no posee CTA CTE. Cree una cuenta para dicho usuario o cambie la condición de venta.');
                }

                //si es cuenta corriente se pone el total pagado en 0 y se genera el movimiento, sino el total
                if($debitNote->getSalesCondition()->getId() == 2) {
                    $debitNote->setTotalPayed(0);

                    $detail = AccountMovement::DEBITNOTE_MOVEMENT;
                    $amount = $debitNote->getTotal();
                    $account = $debitNote->getCustomer()->getAccount();

                    $movement = new DebitNoteAccountMovement();
                    $movement->generateAccountMovementForAccount($detail, $amount, $account, $debitNote);

                    $account->setBalance($account->getBalance() + $amount);

                    $em->persist($account);
                    $em->persist($movement);
                } else {
                    $debitNote->setTotalPayed($debitNote->getTotal());
                }
                $em->persist($debitNote);
                $em->flush();
                
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollBack();

                $this->get('session')->getFlashBag()->add(
                        'danger', 'Se produjeron errores al intentar guardar los cambios. ' . $e->getMessage()
                );
            }

            return $this->redirectToRoute('debitnote_show', array('id' => $debitNote->getId()));
        }

        return $this->render('debitnote/new.html.twig', array(
                    'debitNote' => $debitNote,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a debitNote entity.
     *
     * @Route("/{id}", name="debitnote_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(DebitNote $debitNote) {
        $deleteForm = $this->createDeleteForm($debitNote);

        return $this->render('debitnote/show.html.twig', array(
                    'debitNote' => $debitNote,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a debitNote entity.
     *
     * @Route("/{id}", name="debitnote_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DebitNote $debitNote) {
        $form = $this->createDeleteForm($debitNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($debitNote);
            $em->flush($debitNote);
        }

        return $this->redirectToRoute('debitnote_index');
    }

    /**
     * Creates a form to delete a debitNote entity.
     *
     * @param DebitNote $debitNote The debitNote entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DebitNote $debitNote) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('debitnote_delete', array('id' => $debitNote->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
