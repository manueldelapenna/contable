<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DebitNote
 *
 * @ORM\Table(name="debit_note")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DebitNoteRepository")
 */
class DebitNote
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
     * @var float
     *
     * @ORM\Column(name="total_payed", type="decimal", precision=12, scale=4)
     * 
     */
    private $totalPayed;
    
    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="debitNotes")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private $customer;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesPoint", inversedBy="debitNotes")
     * @ORM\JoinColumn(name="sales_point_id", referencedColumnName="id", nullable=false)
     */
    private $salesPoint;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesCondition", inversedBy="debitNotes")
     * @ORM\JoinColumn(name="sales_condition_id", referencedColumnName="id", nullable=false)
     */
    private $salesCondition;
    
    public function __construct()
    {
        $this->setDate(new \DateTime("now"));
    }
    
    public static function createFromOrder(PurchaseOrder $purchaseOrder, SalesCondition $salesCondition){
        
        $debitNote = new self();
        $debitNote->setCustomer($purchaseOrder->getCustomer());
        $debitNote->setSalesCondition($salesCondition);
        $debitNote->setSalesPoint($purchaseOrder->getSalesPoint());
        $debitNote->setDiscountAmount($purchaseOrder->getDiscountAmount());
        $debitNote->setShippingAmount($purchaseOrder->getShippingAmount());
        $debitNote->setSubtotal($purchaseOrder->getSubtotal());
        $debitNote->setTotal($purchaseOrder->getTotal());
        
        return $debitNote;
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
     * @return DebitNote
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
     * Set total
     *
     * @param float $total
     *
     * @return DebitNote
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
     * Set customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return DebitNote
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
     * @return DebitNote
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
     * Set salesCondition
     *
     * @param \AppBundle\Entity\SalesCondition $salesCondition
     *
     * @return DebitNote
     */
    public function setSalesCondition(\AppBundle\Entity\SalesCondition $salesCondition = null)
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

    /**
     * Set totalPayed
     *
     * @param string $totalPayed
     *
     * @return DebitNote
     */
    public function setTotalPayed($totalPayed)
    {
        $this->totalPayed = $totalPayed;

        return $this;
    }

    /**
     * Get totalPayed
     *
     * @return string
     */
    public function getTotalPayed()
    {
        return $this->totalPayed;
    }

    /**
     * Set subtotal
     *
     * @param string $subtotal
     *
     * @return DebitNote
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return string
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set discountAmount
     *
     * @param string $discountAmount
     *
     * @return DebitNote
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount
     *
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set shippingAmount
     *
     * @param string $shippingAmount
     *
     * @return DebitNote
     */
    public function setShippingAmount($shippingAmount)
    {
        $this->shippingAmount = $shippingAmount;

        return $this;
    }

    /**
     * Get shippingAmount
     *
     * @return string
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }
}
