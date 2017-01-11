<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PurchaseOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

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
        $form = $this->createForm('AppBundle\Form\PurchaseOrderType', $purchaseOrder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($purchaseOrder);
            
            foreach($purchaseOrder->getOrderItems() as $item){
                $item->setOrder($purchaseOrder);
                $em->persist($item);
            }
            
            $em->flush();

            return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
        }

        return $this->render('purchaseorder/new.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $form->createView(),
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
            
            foreach($purchaseOrder->getOrderItems() as $item){
                $item->setOrder($purchaseOrder);
                $this->getDoctrine()->getManager()->persist($item);
            }
            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('purchaseorder_edit', array('id' => $purchaseOrder->getId()));
        }

        return $this->render('purchaseorder/new.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
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
            $em->remove($purchaseOrder);
            $em->flush($purchaseOrder);
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
