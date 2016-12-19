<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * IvaCondition
 *
 * @ORM\Table(name="iva_condition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IvaConditionRepository")
 * @UniqueEntity("name")
 */
class IvaCondition
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
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="ivaCondition")
     */
    private $customers;


    public function __construct()
    {
        $this->customers = new ArrayCollection();
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
     * @return IvaCondition
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
    
    function getCustomers() {
        return $this->customers;
    }


    /**
     * Get customers
     *
     * @return Array Customer
     */
    public function getCostumers()
    {
        return $this->customers;

    }


    /**
     * Add customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return IvaCondition
     */
    public function addCustomer(\AppBundle\Entity\Customer $customer)
    {
        $this->customers[] = $customer;

        return $this;
    }

    /**
     * Remove customer
     *
     * @param \AppBundle\Entity\Customer $customer
     */
    public function removeCustomer(\AppBundle\Entity\Customer $customer)
    {
        $this->customers->removeElement($customer);
    }
}
