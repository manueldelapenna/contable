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
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $debitNotes = $em->getRepository('AppBundle:DebitNote')->findAll();

        return $this->render('debitnote/index.html.twig', array(
                    'debitNotes' => $debitNotes,
        ));
    }

    /**
     * Creates a new debitNote entity.
     *
     * @Route("/new", name="debitnote_new")
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
     * @Route("/{id}", name="debitnote_show")
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
     * Displays a form to edit an existing debitNote entity.
     *
     * @Route("/{id}/edit", name="debitnote_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DebitNote $debitNote) {
        $deleteForm = $this->createDeleteForm($debitNote);
        $editForm = $this->createForm('AppBundle\Form\DebitNoteType', $debitNote);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('debitnote_edit', array('id' => $debitNote->getId()));
        }

        return $this->render('debitnote/edit.html.twig', array(
                    'debitNote' => $debitNote,
                    'edit_form' => $editForm->createView(),
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
