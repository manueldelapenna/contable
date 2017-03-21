<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SalesCondition
 *
 * @ORM\Table(name="sales_condition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SalesConditionRepository")
 * @UniqueEntity("name")
 */
class SalesCondition
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="salesCondition")
     */
    private $invoices;
    
    /**
     * @ORM\OneToMany(targetEntity="DebitNote", mappedBy="salesCondition")
     */
    private $debitNotes ;
    
    /**
     * @ORM\OneToMany(targetEntity="CreditNote", mappedBy="salesCondition")
     */
    private $creditNotes;
    
    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return SalesCondition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add invoice
     *
     * @param \AppBundle\Entity\Invoice $invoice
     *
     * @return SalesCondition
     */
    public function addInvoice(\AppBundle\Entity\Invoice $invoice)
    {
        $this->invoices[] = $invoice;

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param \AppBundle\Entity\Invoice $invoice
     */
    public function removeInvoice(\AppBundle\Entity\Invoice $invoice)
    {
        $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add debitNote
     *
     * @param \AppBundle\Entity\DebitNote $debitNote
     *
     * @return SalesCondition
     */
    public function addDebitNote(\AppBundle\Entity\DebitNote $debitNote)
    {
        $this->debitNotes[] = $debitNote;

        return $this;
    }

    /**
     * Remove debitNote
     *
     * @param \AppBundle\Entity\DebitNote $debitNote
     */
    public function removeDebitNote(\AppBundle\Entity\DebitNote $debitNote)
    {
        $this->debitNotes->removeElement($debitNote);
    }

    /**
     * Get debitNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDebitNotes()
    {
        return $this->debitNotes;
    }

    /**
     * Add creditNote
     *
     * @param \AppBundle\Entity\CreditNote $creditNote
     *
     * @return SalesCondition
     */
    public function addCreditNote(\AppBundle\Entity\CreditNote $creditNote)
    {
        $this->creditNotes[] = $creditNote;

        return $this;
    }

    /**
     * Remove creditNote
     *
     * @param \AppBundle\Entity\CreditNote $creditNote
     */
    public function removeCreditNote(\AppBundle\Entity\CreditNote $creditNote)
    {
        $this->creditNotes->removeElement($creditNote);
    }

    /**
     * Get creditNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditNotes()
    {
        return $this->creditNotes;
    }
}
