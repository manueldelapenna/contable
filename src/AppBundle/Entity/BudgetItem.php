<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BudgetItem
 *
 * @ORM\Table(name="budget_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BudgetItemRepository")
 */
class BudgetItem
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
     * @ORM\Column(name="product_code", type="string", length=255)
     */
    private $productCode;

    
    /**
     * @var float
     * 
     * @ORM\Column(name="product_quantity", type="float")
     *
     * @Assert\Range(
     *      min = 0.0001,
     *      minMessage = "El valor debe ser mayor a 0",
     * )
     */
    private $productQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="product_description", type="string", length=255)
     */
    private $productDescription;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_price", type="decimal", precision=12, scale=4)
     *
     * @Assert\Range(
     *      min = 0.0001,
     *      minMessage = "El valor debe ser mayor a 0",
     * )
     */
    private $unitPrice;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tax", inversedBy="budgetItems")
     * @ORM\JoinColumn(name="tax_id", referencedColumnName="id")
     */
    private $tax;
    
    /**
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="budgetItems")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $budget;


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
     * Set productCode
     *
     * @param string $productCode
     *
     * @return BudgetItem
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get productCode
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set productQuantity
     *
     * @param float $productQuantity
     *
     * @return BudgetItem
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
     * Get productQuantity
     *
     * @return float
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set productDescription
     *
     * @param string $productDescription
     *
     * @return BudgetItem
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    /**
     * Get productDescription
     *
     * @return string
     */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
     * Set unitPrice
     *
     * @param float $unitPrice
     *
     * @return BudgetItem
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set budget
     *
     * @param \AppBundle\Entity\Budget $budget
     *
     * @return BudgetItem
     */
    public function setBudget(\AppBundle\Entity\Budget $budget = null)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return \AppBundle\Entity\Budget
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set tax
     *
     * @param \AppBundle\Entity\Tax $tax
     *
     * @return BudgetItem
     */
    public function setTax(\AppBundle\Entity\Tax $tax = null)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return \AppBundle\Entity\Tax
     */
    public function getTax()
    {
        return $this->tax;
    }
}
