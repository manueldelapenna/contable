<?php

namespace AfipBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AfipBundle\Entity\Helper\DataType\FileDataTypeHelper;

class DefaultController extends Controller
{
    /**
     * @Route("/afip")
     */
    public function indexAction()
    {
        FileDataTypeHelper::getMimeType('DefaultController.php');
        echo $this->container->get('kernel')->getRootDir(); die;
        return $this->render('AfipBundle:Default:index.html.twig');
    }
}
