<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BudgetState
 *
 * @ORM\Table(name="budget_state")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BudgetStateRepository")
 * @UniqueEntity("name")
 */
class BudgetState
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
     * @ORM\OneToMany(targetEntity="PurchaseBudget", mappedBy="budgetState")
     */
    private $budgets;
    
    
    public function __construct()
    {
        $this->budgets = new ArrayCollection();
        
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
     * @return BudgetState
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
     * Add budget
     *
     * @param \AppBundle\Entity\PurchaseBudget $budget
     *
     * @return BudgetState
     */
    public function addBudget(\AppBundle\Entity\PurchaseBudget $budget)
    {
        $this->budgets[] = $budget;

        return $this;
    }

    /**
     * Remove budget
     *
     * @param \AppBundle\Entity\PurchaseBudget $budget
     */
    public function removeBudget(\AppBundle\Entity\PurchaseBudget $budget)
    {
        $this->budgets->removeElement($budget);
    }

    /**
     * Get budgets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBudgets()
    {
        return $this->budgets;
    }
}
