<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Customer;
use AppBundle\Form\CustomerType;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Customer controller.
 *
 * @Route("/admin/customer")
 */
class CustomerController extends Controller {

    /**
     * Lists all Customer entities.
     *
     * @Route("/", name="customer_index")
     * @Method("GET")
     */
    public function indexAction() {
        $datatable = $this->get('app.datatable.customer');
        $datatable->buildDatatable();

        return $this->render('customer/index.html.twig', array(
                    'datatable' => $datatable,
        ));
    }

    /**
     * @Route("/results", name="customer_results")
     */
    public function indexResultsAction() {
        $datatable = $this->get('app.datatable.customer');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }

    /**
     * Creates a new Customer entity.
     *
     * @Route("/new", name="customer_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $customer = new Customer();
        $form = $this->createForm('AppBundle\Form\CustomerType', $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                    'success', 'Los cambios fueron guardados correctamente'
            );

            return $this->redirectToRoute('customer_show', array('id' => $customer->getId()));
        }

        return $this->render('customer/new.html.twig', array(
                    'customer' => $customer,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Customer entity.
     *
     * @Route("/{id}", name="customer_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Customer $customer) {
        $deleteForm = $this->createDeleteForm($customer);

        return $this->render('customer/show.html.twig', array(
                    'customer' => $customer,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Customer entity.
     *
     * @Route("/{id}/edit", name="customer_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Customer $customer) {
        $deleteForm = $this->createDeleteForm($customer);
        $editForm = $this->createForm('AppBundle\Form\CustomerType', $customer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                    'success', 'Los cambios fueron guardados correctamente'
            );

            return $this->redirectToRoute('customer_edit', array('id' => $customer->getId()));
        }

        return $this->render('customer/edit.html.twig', array(
                    'customer' => $customer,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Customer entity.
     *
     * @Route("/{id}", name="customer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Customer $customer) {
        $form = $this->createDeleteForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add(
                    'success', 'El elemento ha sido borrado.'
            );
            
        }

        return $this->redirectToRoute('customer_index');
    }

    /**
     * Creates a form to delete a Customer entity.
     *
     * @param Customer $customer The Customer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Customer $customer) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('customer_delete', array('id' => $customer->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }
    
    /**
     * @Route("/search/{attribute}/{value}", name="customer_search")
     * @Method({"POST"})
     */
    public function searchAction(Request $request, $attribute, $value)
    {
        if($request->isXmlHttpRequest())
        {
            $encoders = array(new JsonEncoder());
            $normalizer = new ObjectNormalizer();
 
            $serializer = new Serializer(array($normalizer), $encoders);
 
            $em = $this->getDoctrine()->getManager();
            $customers =  $em->getRepository('AppBundle:Customer')->findByAttribute($attribute, $value);
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
            
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            
            $response->setData(array(
                'response' => 'success',
                'customers' => $serializer->serialize($customers, 'json')
            ));
            
            return $response;
        }
    }
    /**
     * @Route("/findall/", name="customer_findall")
     * @Method("GET")
     */
    public function findallAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $customers = $em->getRepository('AppBundle:Customer')->findByAttribute('name', 'text', $request->query->get('q'));

        return new JsonResponse($customers);
            
    }
        

}
