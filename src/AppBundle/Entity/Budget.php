<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Budget
 *
 * @ORM\Table(name="budget")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BudgetRepository")
 */
class Budget
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
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="budgets")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private $customer;
    
    /**
     * @ORM\ManyToOne(targetEntity="BudgetState", inversedBy="budgets")
     * @ORM\JoinColumn(name="budget_state_id", referencedColumnName="id", nullable=false)
     */
    private $budgetState;
	
	/**
     * One Product has One Order.
     * @OneToOne(targetEntity="PurchaseOrder")
     * @JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;
    
    /**
     * @ORM\OneToMany(targetEntity="BudgetItem", mappedBy="budget", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $budgetItems;
    
    public function __construct()
    {
        $this->setDate(new \DateTime("now"));
        $this->budgetItems = new ArrayCollection();
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
     * @return Budget
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
     * @return Budget
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
     * @return Budget
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
     * @return Budget
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
     * @return Budget
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
     * @return Budget
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
     * Set budgetState
     *
     * @param \AppBundle\Entity\BudgetState $budgetState
     *
     * @return Budget
     */
    public function setBudgetState(\AppBundle\Entity\BudgetState $budgetState = null)
    {
        $this->budgetState = $budgetState;

        return $this;
    }

    /**
     * Get budgetState
     *
     * @return \AppBundle\Entity\BudgetState
     */
    public function getBudgetState()
    {
        return $this->budgetState;
    }

    /**
     * Add budgetItem
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItem
     *
     * @return Budget
     */
    public function addBudgetItem(\AppBundle\Entity\BudgetItem $budgetItem)
    {
        $this->budgetItems[] = $budgetItem;

        return $this;
    }

    /**
     * Remove budgetItem
     *
     * @param \AppBundle\Entity\BudgetItem $budgetItem
     */
    public function removeBudgetItem(\AppBundle\Entity\BudgetItem $budgetItem)
    {
        $this->budgetItems->removeElement($budgetItem);
    }

    /**
     * Get budgetItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBudgetItems()
    {
        return $this->budgetItems;
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
