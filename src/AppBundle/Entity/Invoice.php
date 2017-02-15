<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 */
class Invoice
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
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="invoices")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private $customer;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesPoint", inversedBy="invoices")
     * @ORM\JoinColumn(name="sales_point_id", referencedColumnName="id", nullable=false)
     */
    private $salesPoint;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesCondition", inversedBy="invoices")
     * @ORM\JoinColumn(name="sales_condition_id", referencedColumnName="id", nullable=false)
     */
    private $salesCondition;
    
    /**
     * @ORM\OneToMany(targetEntity="InvoiceItem", mappedBy="invoice", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $invoiceItems;
    
    /**
     * One Invoice has One PurchaseOrder.
     * @ORM\OneToOne(targetEntity="PurchaseOrder")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    private $order;
    
    
    public function __construct()
    {
        $this->setDate(new \DateTime("now"));
        $this->invoiceItems = new ArrayCollection();
        
    }
    
    public static function createFromOrder(PurchaseOrder $purchaseOrder, SalesCondition $salesCondition){
        
        $invoice = new self();
        $invoice->setCustomer($purchaseOrder->getCustomer());
        $invoice->setOrder($purchaseOrder);
        $invoice->setSalesCondition($salesCondition);
        $invoice->setSalesPoint($purchaseOrder->getSalesPoint());
        $invoice->setDiscountAmount($purchaseOrder->getDiscountAmount());
        $invoice->setShippingAmount($purchaseOrder->getShippingAmount());
        $invoice->setSubtotal($purchaseOrder->getSubtotal());
        $invoice->setTotal($purchaseOrder->getTotal());
        
        return $invoice;
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * Add invoiceItem
     *
     * @param \AppBundle\Entity\InvoiceItem $invoiceItem
     *
     * @return Invoice
     */
    public function addInvoiceItem(\AppBundle\Entity\InvoiceItem $invoiceItem)
    {
        $this->invoiceItems[] = $invoiceItem;

        return $this;
    }

    /**
     * Remove invoiceItem
     *
     * @param \AppBundle\Entity\InvoiceItem $invoiceItem
     */
    public function removeInvoiceItem(\AppBundle\Entity\InvoiceItem $invoiceItem)
    {
        $this->invoiceItems->removeElement($invoiceItem);
    }

    /**
     * Get invoiceItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoiceItems()
    {
        return $this->invoiceItems;
    }


    /**
     * Set order
     *
     * @param \AppBundle\Entity\PurchaseOrder $order
     *
     * @return Invoice
     */
    public function setOrder(\AppBundle\Entity\PurchaseOrder $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\PurchaseOrder
     */
    public function getOrder()
    {
        return $this->order;
    }
}
