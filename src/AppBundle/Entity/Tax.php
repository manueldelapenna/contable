<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Tax
 *
 * @ORM\Table(name="tax")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxRepository")
 * @UniqueEntity("name")
 */
class Tax
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;
    
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="tax")
     */
    private $products;
    
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="tax")
     */
    private $orderItems;
    
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="tax")
     */
    private $budgetItems;
    
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="tax")
     */
    private $invoiceItems;
    
    public function __construct() {
        $this->products = new ArrayCollection();
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
     * @return Tax
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Tax
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return Tax
     */
    public function addProduct(\AppBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(\AppBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add orderItem
     *
     * @param \AppBundle\Entity\Product $orderItem
     *
     * @return Tax
     */
    public function addOrderItem(\AppBundle\Entity\Product $orderItem)
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }

    /**
     * Remove orderItem
     *
     * @param \AppBundle\Entity\Product $orderItem
     */
    public function removeOrderItem(\AppBundle\Entity\Product $orderItem)
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
     * Add budgetItem
     *
     * @param \AppBundle\Entity\Product $budgetItem
     *
     * @return Tax
     */
    public function addBudgetItem(\AppBundle\Entity\Product $budgetItem)
    {
        $this->budgetItems[] = $budgetItem;

        return $this;
    }

    /**
     * Remove budgetItem
     *
     * @param \AppBundle\Entity\Product $budgetItem
     */
    public function removeBudgetItem(\AppBundle\Entity\Product $budgetItem)
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
     * Add invoiceItem
     *
     * @param \AppBundle\Entity\Product $invoiceItem
     *
     * @return Tax
     */
    public function addInvoiceItem(\AppBundle\Entity\Product $invoiceItem)
    {
        $this->invoiceItems[] = $invoiceItem;

        return $this;
    }

    /**
     * Remove invoiceItem
     *
     * @param \AppBundle\Entity\Product $invoiceItem
     */
    public function removeInvoiceItem(\AppBundle\Entity\Product $invoiceItem)
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
}
