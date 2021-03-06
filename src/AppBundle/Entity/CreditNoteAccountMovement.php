<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CreditNoteAccountMovement
 *
 * @ORM\Table(name="credit_note_account_movement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CreditNoteAccountMovementRepository")
 */
class CreditNoteAccountMovement extends AccountMovement
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
     * One CreditNoteAccountMovement has One CreditNote.
     h* @ORM\OneToOne(targetEntity="CreditNote")
     * @ORM\JoinColumn(name="credit_note_id", referencedColumnName="id")
     */
    private $creditNote;
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function generateAccountMovementForAccount($detail, $amount, Account $account, $creditNote){
        parent::generateAccountMovementForAccount($detail, $amount, $account, $creditNote);
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
     * Set creditNote
     *
     * @param \AppBundle\Entity\CreditNote $creditNote
     *
     * @return CreditNoteAccountMovement
     */
    public function setCreditNote(\AppBundle\Entity\CreditNote $creditNote)
    {
        $this->creditNote = $creditNote;

        return $this;
    }
    
    /**
     * Get creditNote
     *
     * @return \AppBundle\Entity\CreditNote
     */
    public function getCreditNote()
    {
        return $this->creditNote;
    }
    
    /**
     * Set document
     *
     * @param \AppBundle\Entity\CreditNote $creditNote
     *
     * @return CreditNoteAccountMovement
     */
    public function setDocument(\AppBundle\Entity\CreditNote $creditNote)
    {
        $this->setCreditNote($creditNote);
    }
    
    /**
     * Get document
     *
     * @return \AppBundle\Entity\CreditNote
     */
    public function getDocument()
    {
        return $this->getCreditNote();
    }
}
