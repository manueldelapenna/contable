<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrderItem
 *
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderItemRepository")
 */
class OrderItem
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
     * @ORM\Column(name="unit_price", type="float")
     *
     * @Assert\Range(
     *      min = 0.0001,
     *      minMessage = "El valor debe ser mayor a 0",
     * )
     */
    private $unitPrice;
    
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrder", inversedBy="orderItems")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;


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
     * @return OrderItem
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
     * @return OrderItem
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
     * @return OrderItem
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
     * @return OrderItem
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
     * Set order
     *
     * @param \AppBundle\Entity\PurchaseOrder $order
     *
     * @return OrderItem
     */
    public function setOrder(\AppBundle\Entity\PurchaseOrder $order = null)
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
