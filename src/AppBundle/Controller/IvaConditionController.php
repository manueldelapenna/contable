<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\IvaCondition;
use AppBundle\Form\IvaConditionType;

/**
 * IvaCondition controller.
 *
 * @Route("/admin/ivacondition")
 */
class IvaConditionController extends Controller {

    /**
     * Lists all IvaCondition entities.
     *
     * @Route("/", name="ivacondition_index")
     * @Method("GET")
     */
    public function indexAction() {
        $datatable = $this->get('app.datatable.ivacondition');
        $datatable->buildDatatable();

        return $this->render('ivacondition/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/results", name="ivacondition_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.ivacondition');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new IvaCondition entity.
     *
     * @Route("/new", name="ivacondition_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $ivaCondition = new IvaCondition();
        $form = $this->createForm('AppBundle\Form\IvaConditionType', $ivaCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ivaCondition);
            $em->flush();

            return $this->redirectToRoute('ivacondition_show', array('id' => $ivaCondition->getId()));
        }

        return $this->render('ivacondition/new.html.twig', array(
                    'ivaCondition' => $ivaCondition,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a IvaCondition entity.
     *
     * @Route("/{id}", name="ivacondition_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(IvaCondition $ivaCondition) {
        $deleteForm = $this->createDeleteForm($ivaCondition);

        return $this->render('ivacondition/show.html.twig', array(
                    'ivaCondition' => $ivaCondition,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing IvaCondition entity.
     *
     * @Route("/{id}/edit", name="ivacondition_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, IvaCondition $ivaCondition) {
        $deleteForm = $this->createDeleteForm($ivaCondition);
        $editForm = $this->createForm('AppBundle\Form\IvaConditionType', $ivaCondition);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ivaCondition);
            $em->flush();

            return $this->redirectToRoute('ivacondition_edit', array('id' => $ivaCondition->getId()));
        }

        return $this->render('ivacondition/edit.html.twig', array(
                    'ivaCondition' => $ivaCondition,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a IvaCondition entity.
     *
     * @Route("/{id}", name="ivacondition_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, IvaCondition $ivaCondition) {
        $form = $this->createDeleteForm($ivaCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ivaCondition);
            $em->flush();
        }

        return $this->redirectToRoute('ivacondition_index');
    }

    /**
     * Creates a form to delete a IvaCondition entity.
     *
     * @param IvaCondition $ivaCondition The IvaCondition entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(IvaCondition $ivaCondition) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('ivacondition_delete', array('id' => $ivaCondition->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
