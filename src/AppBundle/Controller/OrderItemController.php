<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrderItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Orderitem controller.
 *
 * @Route("orderitem")
 */
class OrderItemController extends Controller
{
    /**
     * Lists all orderItem entities.
     *
     * @Route("/", name="orderitem_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orderItems = $em->getRepository('AppBundle:OrderItem')->findAll();

        return $this->render('orderitem/index.html.twig', array(
            'orderItems' => $orderItems,
        ));
    }

    /**
     * Creates a new orderItem entity.
     *
     * @Route("/new", name="orderitem_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $orderItem = new Orderitem();
        $form = $this->createForm('AppBundle\Form\OrderItemType', $orderItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderItem);
            $em->flush($orderItem);

            return $this->redirectToRoute('orderitem_show', array('id' => $orderItem->getId()));
        }

        return $this->render('orderitem/new.html.twig', array(
            'orderItem' => $orderItem,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a orderItem entity.
     *
     * @Route("/{id}", name="orderitem_show")
     * @Method("GET")
     */
    public function showAction(OrderItem $orderItem)
    {
        $deleteForm = $this->createDeleteForm($orderItem);

        return $this->render('orderitem/show.html.twig', array(
            'orderItem' => $orderItem,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing orderItem entity.
     *
     * @Route("/{id}/edit", name="orderitem_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OrderItem $orderItem)
    {
        $deleteForm = $this->createDeleteForm($orderItem);
        $editForm = $this->createForm('AppBundle\Form\OrderItemType', $orderItem);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orderitem_edit', array('id' => $orderItem->getId()));
        }

        return $this->render('orderitem/edit.html.twig', array(
            'orderItem' => $orderItem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a orderItem entity.
     *
     * @Route("/{id}", name="orderitem_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderItem $orderItem)
    {
        $form = $this->createDeleteForm($orderItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderItem);
            $em->flush($orderItem);
        }

        return $this->redirectToRoute('orderitem_index');
    }

    /**
     * Creates a form to delete a orderItem entity.
     *
     * @param OrderItem $orderItem The orderItem entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderItem $orderItem)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderitem_delete', array('id' => $orderItem->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
