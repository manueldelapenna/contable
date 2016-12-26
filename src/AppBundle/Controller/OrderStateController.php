<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrderState;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Orderstate controller.
 *
 * @Route("/admin/orderstate")
 */
class OrderStateController extends Controller
{
    /**
     * Lists all orderState entities.
     *
     * @Route("/", name="orderstate_index")
     * @Method("GET")
     */
     public function indexAction() {
        $datatable = $this->get('app.datatable.orderstate');
        $datatable->buildDatatable();

        return $this->render('orderstate/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/results", name="orderstate_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.orderstate');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new orderState entity.
     *
     * @Route("/new", name="orderstate_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $orderState = new Orderstate();
        $form = $this->createForm('AppBundle\Form\OrderStateType', $orderState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderState);
            $em->flush($orderState);

            return $this->redirectToRoute('orderstate_show', array('id' => $orderState->getId()));
        }

        return $this->render('orderstate/new.html.twig', array(
            'orderState' => $orderState,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a orderState entity.
     *
     * @Route("/{id}", name="orderstate_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(OrderState $orderState)
    {
        $deleteForm = $this->createDeleteForm($orderState);

        return $this->render('orderstate/show.html.twig', array(
            'orderState' => $orderState,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing orderState entity.
     *
     * @Route("/{id}/edit", name="orderstate_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OrderState $orderState)
    {
        $deleteForm = $this->createDeleteForm($orderState);
        $editForm = $this->createForm('AppBundle\Form\OrderStateType', $orderState);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orderstate_edit', array('id' => $orderState->getId()));
        }

        return $this->render('orderstate/edit.html.twig', array(
            'orderState' => $orderState,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a orderState entity.
     *
     * @Route("/{id}", name="orderstate_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderState $orderState)
    {
        $form = $this->createDeleteForm($orderState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderState);
            $em->flush($orderState);
        }

        return $this->redirectToRoute('orderstate_index');
    }

    /**
     * Creates a form to delete a orderState entity.
     *
     * @param OrderState $orderState The orderState entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderState $orderState)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderstate_delete', array('id' => $orderState->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
