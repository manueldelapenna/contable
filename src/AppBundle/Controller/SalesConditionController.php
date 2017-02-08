<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SalesCondition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Salescondition controller.
 *
 * @Route("/admin/salescondition")
 */
class SalesConditionController extends Controller
{
    /**
     * Lists all salesCondition entities.
     *
     * @Route("/", name="salescondition_index")
     * @Method("GET")
     */
    public function indexAction() {
        $datatable = $this->get('app.datatable.salescondition');
        $datatable->buildDatatable();

        return $this->render('salescondition/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }
    
    /**
     * @Route("/results", name="salescondition_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.salescondition');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new salesCondition entity.
     *
     * @Route("/new", name="salescondition_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $salesCondition = new Salescondition();
        $form = $this->createForm('AppBundle\Form\SalesConditionType', $salesCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($salesCondition);
            $em->flush($salesCondition);

            return $this->redirectToRoute('salescondition_show', array('id' => $salesCondition->getId()));
        }

        return $this->render('salescondition/new.html.twig', array(
            'salesCondition' => $salesCondition,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a salesCondition entity.
     *
     * @Route("/{id}", name="salescondition_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(SalesCondition $salesCondition)
    {
        $deleteForm = $this->createDeleteForm($salesCondition);

        return $this->render('salescondition/show.html.twig', array(
            'salesCondition' => $salesCondition,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing salesCondition entity.
     *
     * @Route("/{id}/edit", name="salescondition_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SalesCondition $salesCondition)
    {
        $deleteForm = $this->createDeleteForm($salesCondition);
        $editForm = $this->createForm('AppBundle\Form\SalesConditionType', $salesCondition);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('salescondition_edit', array('id' => $salesCondition->getId()));
        }

        return $this->render('salescondition/edit.html.twig', array(
            'salesCondition' => $salesCondition,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a salesCondition entity.
     *
     * @Route("/{id}", name="salescondition_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SalesCondition $salesCondition)
    {
        $form = $this->createDeleteForm($salesCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($salesCondition);
            $em->flush($salesCondition);
        }

        return $this->redirectToRoute('salescondition_index');
    }

    /**
     * Creates a form to delete a salesCondition entity.
     *
     * @param SalesCondition $salesCondition The salesCondition entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SalesCondition $salesCondition)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('salescondition_delete', array('id' => $salesCondition->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
