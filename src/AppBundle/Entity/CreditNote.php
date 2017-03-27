<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CreditNote
 *
 * @ORM\Table(name="credit_note")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CreditNoteRepository")
 */
class CreditNote
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="decimal", precision=12, scale=4)
     *
     *  * @Assert\Range(
     *      min = 0.0001,
     *      minMessage = "El valor debe ser mayor a 0",
     * )
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_amount", type="decimal", precision=12, scale=4)
     * 
     * @Assert\Range(
     *      min = 0.00,
     *      minMessage = "El valor debe ser mayor o igual a 0",
     * )
     */
    private $discountAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="shipping_amount", type="decimal", precision=12, scale=4)
     * 
     * @Assert\Range(
     *      min = 0.00,
     *      minMessage = "El valor debe ser mayor o igual a 0",
     * )
     */
    private $shippingAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="decimal", precision=12, scale=4)
     * 
     * @Assert\Range(
     *      min = 0.01,
     *      minMessage = "El valor debe ser mayor a 0",
     * )
     */
    private $total;
    
    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="creditNotes")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private $customer;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesPoint", inversedBy="creditNotes")
     * @ORM\JoinColumn(name="sales_point_id", referencedColumnName="id", nullable=false)
     */
    private $salesPoint;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesCondition", inversedBy="creditNotes")
     * @ORM\JoinColumn(name="sales_condition_id", referencedColumnName="id", nullable=false)
     */
    private $salesCondition;
    
    /**
     * @ORM\OneToMany(targetEntity="CreditNoteItem", mappedBy="creditNote", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $creditNoteItems;
    
    public function __construct()
    {
        $this->setDate(new \DateTime("now"));
        $this->creditNoteItems = new ArrayCollection();
        
    }
    
    public static function createFromOrder(PurchaseOrder $purchaseOrder, SalesCondition $salesCondition){
        
        $creditNote = new self();
        $creditNote->setCustomer($purchaseOrder->getCustomer());
        $creditNote->setSalesCondition($salesCondition);
        $creditNote->setSalesPoint($purchaseOrder->getSalesPoint());
        $creditNote->setDiscountAmount($purchaseOrder->getDiscountAmount());
        $creditNote->setShippingAmount($purchaseOrder->getShippingAmount());
        $creditNote->setSubtotal($purchaseOrder->getSubtotal());
        $creditNote->setTotal($purchaseOrder->getTotal());
        
        return $creditNote;
    }
	
    public function __toString() {
        return (string)$this->getId();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CreditNote
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     *
     * @return CreditNote
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set shippingAmount
     *
     * @param float $shippingAmount
     *
     * @return CreditNote
     */
    public function setShippingAmount($shippingAmount)
    {
        $this->shippingAmount = $shippingAmount;

        return $this;
    }

    /**
     * Get shippingAmount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return CreditNote
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set discountAmount
     *
     * @param float $discountAmount
     *
     * @return CreditNote
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return CreditNote
     */
    public function setCustomer(\AppBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \AppBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set salesPoint
     *
     * @param \AppBundle\Entity\SalesPoint $salesPoint
     *
     * @return CreditNote
     */
    public function setSalesPoint(\AppBundle\Entity\SalesPoint $salesPoint = null)
    {
        $this->salesPoint = $salesPoint;

        return $this;
    }

    /**
     * Get salesPoint
     *
     * @return \AppBundle\Entity\SalesPoint
     */
    public function getSalesPoint()
    {
        return $this->salesPoint;
    }

    /**
     * Add creditNoteItem
     *
     * @param \AppBundle\Entity\CreditNoteItem $creditNoteItem
     *
     * @return CreditNote
     */
    public function addCreditNoteItem(\AppBundle\Entity\CreditNoteItem $creditNoteItem)
    {
        $this->creditNoteItems[] = $creditNoteItem;

        return $this;
    }

    /**
     * Remove creditNoteItem
     *
     * @param \AppBundle\Entity\CreditNoteItem $creditNoteItem
     */
    public function removeCreditNoteItem(\AppBundle\Entity\CreditNoteItem $creditNoteItem)
    {
        $this->creditNoteItems->removeElement($creditNoteItem);
    }

    /**
     * Get creditNoteItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditNoteItems()
    {
        return $this->creditNoteItems;
    }


    /**
     * Set salesCondition
     *
     * @param \AppBundle\Entity\SalesCondition $salesCondition
     *
     * @return CreditNote
     */
    public function setSalesCondition(\AppBundle\Entity\SalesCondition $salesCondition)
    {
        $this->salesCondition = $salesCondition;

        return $this;
    }

    /**
     * Get salesCondition
     *
     * @return \AppBundle\Entity\SalesCondition
     */
    public function getSalesCondition()
    {
        return $this->salesCondition;
    }
}
