<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Invoice controller.
 *
 * @Route("/admin/invoice")
 */
class InvoiceController extends Controller
{
    /**
     * Lists all invoice entities.
     *
     * @Route("/", name="invoice_index")
     * @Method("GET")
     */
    public function indexAction() {
        $datatable = $this->get('app.datatable.invoice');
        $datatable->buildDatatable();

        return $this->render('invoice/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }
    
    /**
     * @Route("/results", name="invoice_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.invoice');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }


    /**
     * Finds and displays a invoice entity.
     *
     * @Route("/{id}", name="invoice_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Invoice $invoice)
    {

        return $this->render('invoice/show.html.twig', array(
            'invoice' => $invoice,
        ));
    }
}
