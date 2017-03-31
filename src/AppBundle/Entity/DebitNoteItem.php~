<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DebitNoteItem
 *
 * @ORM\Table(name="debit_note_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DebitNoteItemRepository")
 */
class DebitNoteItem
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
     * @ORM\JoinColumn(name="tax_id", referencedColumnName="id", nullable=false)
     */
    private $tax;
    
    /**
     * @ORM\ManyToOne(targetEntity="DebitNote", inversedBy="debitNoteItems")
     * @ORM\JoinColumn(name="debitNote_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $debitNote;
    
    public static function createForDebitNoteFromOrderItem(DebitNote $debitNote, OrderItem $orderItem){
        
        $debitNoteItem = new self();
        $debitNoteItem->setDebitNote($debitNote);
        $debitNoteItem->setProductCode($orderItem->getProductCode());
        $debitNoteItem->setProductDescription($orderItem->getProductDescription());
        $debitNoteItem->setProductQuantity($orderItem->getProductQuantity());
        $debitNoteItem->setTax($orderItem->getTax());
        $debitNoteItem->setUnitPrice($orderItem->getUnitPrice());
        
        return $debitNoteItem;
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
     * Set productCode
     *
     * @param string $productCode
     *
     * @return DebitNoteItem
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
     * @return DebitNoteItem
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
     * @return DebitNoteItem
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
     * @return DebitNoteItem
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
     * Set debitNote
     *
     * @param \AppBundle\Entity\DebitNote $debitNote
     *
     * @return DebitNoteItem
     */
    public function setDebitNote(\AppBundle\Entity\DebitNote $debitNote = null)
    {
        $this->debitNote = $debitNote;

        return $this;
    }

    /**
     * Get debitNote
     *
     * @return \AppBundle\Entity\DebitNote
     */
    public function getDebitNote()
    {
        return $this->debitNote;
    }

    /**
     * Set tax
     *
     * @param \AppBundle\Entity\Tax $tax
     *
     * @return DebitNoteItem
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
