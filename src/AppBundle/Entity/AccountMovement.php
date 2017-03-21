<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"invoiceAccountMovement" = "InvoiceAccountMovement", "debitNoteAccountMovement" = "DebitNoteAccountMovement"})
 */
abstract class AccountMovement
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
     * @var string
     *
     * @ORM\Column(name="detail", type="string", length=255)
     */
    private $detail;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=12, scale=4)
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="movements")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=false)
     */
    private $account;
    
    
    public function __construct()
    {
        $this->setDate(new \DateTime("now"));
        
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
    
    public function generateAccountMovementForAccount($detail, $amount, Account $account, $document){
        
        $this->setDetail($detail);
        $this->setAmount($amount);
        $this->setAccount($account);
        $this->setDocument($document);
       
    }
    
    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return AccountMovement
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
     * Set detail
     *
     * @param string $detail
     *
     * @return AccountMovement
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return AccountMovement
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set account
     *
     * @param \AppBundle\Entity\Account $account
     *
     * @return AccountMovement
     */
    public function setAccount(\AppBundle\Entity\Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \AppBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }
}
