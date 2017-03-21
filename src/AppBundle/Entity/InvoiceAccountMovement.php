<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountMovement
 *
 * @ORM\Table(name="invoice_account_movement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceAccountMovementRepository")
 */
class InvoiceAccountMovement extends AccountMovement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * One InvoiceAccountMovement has One Invoice.
     * @ORM\OneToOne(targetEntity="Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false)
     */
    private $invoice;
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function generateAccountMovementForAccount($detail, $amount, Account $account){
        parent::generateAccountMovementForAccount($detail, $amount, $account);
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set invoice
     *
     * @param \AppBundle\Entity\Invoice $invoice
     *
     * @return InvoiceAccountMovement
     */
    public function setInvoice(\AppBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \AppBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}