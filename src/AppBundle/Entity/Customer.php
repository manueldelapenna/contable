<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 */
class Customer
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
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit_dni", type="string", length=255)
     */
    private $cuitDni;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="phones", type="string", length=255, nullable=true)
     */
    private $phones;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     */
    private $observations;
    
    /**
     * @ORM\ManyToOne(targetEntity="IvaCondition", inversedBy="customers")
     * @ORM\JoinColumn(name="iva_condition_id", nullable=false, referencedColumnName="id")
     */
    private $ivaCondition;
    
    /**
     * @ORM\OneToMany(targetEntity="PurchaseOrder", mappedBy="customer")
     */
    private $orders;
    
    /**
     * @ORM\OneToMany(targetEntity="CreditNote", mappedBy="customer")
     */
    private $creditNotes;
    
    /**
     * @ORM\OneToMany(targetEntity="DebitNote", mappedBy="customer")
     */
    private $debitNotes;
    
    /**
     * @ORM\OneToOne(targetEntity="Account", mappedBy="customer")
     */
    private $account;
    
    
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime("now"));
        $this->orders = new ArrayCollection();
        
    }
    
    public function __toString() {
        
        return $this->name;
        
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Customer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Customer
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
     * Set cuitDni
     *
     * @param string $cuitDni
     *
     * @return Customer
     */
    public function setCuitDni($cuitDni)
    {
        $this->cuitDni = $cuitDni;

        return $this;
    }

    /**
     * Get cuitDni
     *
     * @return string
     */
    public function getCuitDni()
    {
        return $this->cuitDni;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Customer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Customer
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Customer
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     *
     * @return Customer
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set phones
     *
     * @param string $phones
     *
     * @return Customer
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set observations
     *
     * @param string $observations
     *
     * @return Customer
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * Get observations
     *
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }
    
    function getIvaCondition() {
        return $this->ivaCondition;
    }


    /**
     * Set ivaCondition
     *
     * @param \AppBundle\Entity\IvaCondition $ivaCondition
     *
     * @return Customer
     */
    public function setIvaCondition(\AppBundle\Entity\IvaCondition $ivaCondition = null)
    {
        $this->ivaCondition = $ivaCondition;

        return $this;
    }
        

    /**
     * Add order
     *
     * @param \AppBundle\Entity\PurchaseOrder $order
     *
     * @return Customer
     */
    public function addOrder(\AppBundle\Entity\PurchaseOrder $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\PurchaseOrder $order
     */
    public function removeOrder(\AppBundle\Entity\PurchaseOrder $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set account
     *
     * @param \AppBundle\Entity\Account $account
     *
     * @return Customer
     */
    public function setAccount(\AppBundle\Entity\Account $account = null)
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

    /**
     * Add creditNote
     *
     * @param \AppBundle\Entity\CreditNotes $creditNote
     *
     * @return Customer
     */
    public function addCreditNote(\AppBundle\Entity\CreditNotes $creditNote)
    {
        $this->creditNotes[] = $creditNote;

        return $this;
    }

    /**
     * Remove creditNote
     *
     * @param \AppBundle\Entity\CreditNotes $creditNote
     */
    public function removeCreditNote(\AppBundle\Entity\CreditNotes $creditNote)
    {
        $this->creditNotes->removeElement($creditNote);
    }

    /**
     * Get creditNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditNotes()
    {
        return $this->creditNotes;
    }

    /**
     * Add debitNote
     *
     * @param \AppBundle\Entity\DebitNotes $debitNote
     *
     * @return Customer
     */
    public function addDebitNote(\AppBundle\Entity\DebitNotes $debitNote)
    {
        $this->debitNotes[] = $debitNote;

        return $this;
    }

    /**
     * Remove debitNote
     *
     * @param \AppBundle\Entity\DebitNotes $debitNote
     */
    public function removeDebitNote(\AppBundle\Entity\DebitNotes $debitNote)
    {
        $this->debitNotes->removeElement($debitNote);
    }

    /**
     * Get debitNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDebitNotes()
    {
        return $this->debitNotes;
    }
}
