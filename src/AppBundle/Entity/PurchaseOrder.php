<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PurchaseOrder
 *
 * @ORM\Table(name="purchase_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PurchaseOrderRepository")
 */
class PurchaseOrder
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
     * @ORM\Column(name="subtotal", type="float")
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_amount", type="float")
     */
    private $discountAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="shipping_amount", type="float")
     */
    private $shippingAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;
    
    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="orders")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;
    
    /**
     * @ORM\ManyToOne(targetEntity="OrderState", inversedBy="orders")
     * @ORM\JoinColumn(name="order_state_id", referencedColumnName="id")
     */
    private $orderState;
    
    /**
     * @ORM\ManyToOne(targetEntity="SalesPoint", inversedBy="orders")
     * @ORM\JoinColumn(name="sales_point_id", referencedColumnName="id")
     */
    private $salesPoint;
    
    /**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order", cascade={"all"}, orphanRemoval=true)
     */
    private $orderItems;
    
    /**
     * @ORM\OneToMany(targetEntity="OrderComment", mappedBy="order")
     */
    private $comments;
    
    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->comments = new ArrayCollection();
        
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
     * @return PurchaseOrder
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
     * @return PurchaseOrder
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
     * @return PurchaseOrder
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
     * @return PurchaseOrder
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
     * @return PurchaseOrder
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
     * @return PurchaseOrder
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
     * Set orderState
     *
     * @param \AppBundle\Entity\OrderState $orderState
     *
     * @return PurchaseOrder
     */
    public function setOrderState(\AppBundle\Entity\OrderState $orderState = null)
    {
        $this->orderState = $orderState;

        return $this;
    }

    /**
     * Get orderState
     *
     * @return \AppBundle\Entity\OrderState
     */
    public function getOrderState()
    {
        return $this->orderState;
    }

    /**
     * Set salesPoint
     *
     * @param \AppBundle\Entity\SalesPoint $salesPoint
     *
     * @return PurchaseOrder
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
     * Add orderItem
     *
     * @param \AppBundle\Entity\OrderItem $orderItem
     *
     * @return PurchaseOrder
     */
    public function addOrderItem(\AppBundle\Entity\OrderItem $orderItem)
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }

    /**
     * Remove orderItem
     *
     * @param \AppBundle\Entity\OrderItem $orderItem
     */
    public function removeOrderItem(\AppBundle\Entity\OrderItem $orderItem)
    {
        $this->orderItems->removeElement($orderItem);
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\OrderComment $comment
     *
     * @return PurchaseOrder
     */
    public function addComment(\AppBundle\Entity\OrderComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\OrderComment $comment
     */
    public function removeComment(\AppBundle\Entity\OrderComment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
