<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SalesPoint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Salespoint controller.
 *
 * @Route("/admin/salespoint")
 */
class SalesPointController extends Controller
{
    /**
     * Lists all salesPoint entities.
     *
     * @Route("/", name="salespoint_index")
     * @Method("GET")
     */
    public function indexAction() {
        $datatable = $this->get('app.datatable.salespoint');
        $datatable->buildDatatable();

        return $this->render('salespoint/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/results", name="salespoint_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.salespoint');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new salesPoint entity.
     *
     * @Route("/new", name="salespoint_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $salesPoint = new Salespoint();
        $form = $this->createForm('AppBundle\Form\SalesPointType', $salesPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($salesPoint);
            $em->flush($salesPoint);

            return $this->redirectToRoute('salespoint_show', array('id' => $salesPoint->getId()));
        }

        return $this->render('salespoint/new.html.twig', array(
            'salesPoint' => $salesPoint,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a salesPoint entity.
     *
     * @Route("/{id}", name="salespoint_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(SalesPoint $salesPoint)
    {
        $deleteForm = $this->createDeleteForm($salesPoint);

        return $this->render('salespoint/show.html.twig', array(
            'salesPoint' => $salesPoint,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing salesPoint entity.
     *
     * @Route("/{id}/edit", name="salespoint_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SalesPoint $salesPoint)
    {
        $deleteForm = $this->createDeleteForm($salesPoint);
        $editForm = $this->createForm('AppBundle\Form\SalesPointType', $salesPoint);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('salespoint_edit', array('id' => $salesPoint->getId()));
        }

        return $this->render('salespoint/edit.html.twig', array(
            'salesPoint' => $salesPoint,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a salesPoint entity.
     *
     * @Route("/{id}", name="salespoint_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SalesPoint $salesPoint)
    {
        $form = $this->createDeleteForm($salesPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($salesPoint);
            $em->flush($salesPoint);
        }

        return $this->redirectToRoute('salespoint_index');
    }

    /**
     * Creates a form to delete a salesPoint entity.
     *
     * @param SalesPoint $salesPoint The salesPoint entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SalesPoint $salesPoint)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('salespoint_delete', array('id' => $salesPoint->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
