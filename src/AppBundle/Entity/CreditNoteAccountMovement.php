<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DebitNoteAccountMovement
 *
 * @ORM\Table(name="debit_note_account_movement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DebitNoteAccountMovementRepository")
 */
class DebitNoteAccountMovement extends AccountMovement
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
     * One DebitNoteAccountMovement has One DebitNote.
     * @ORM\OneToOne(targetEntity="DebitNote")
     * @ORM\JoinColumn(name="debit_note_id", referencedColumnName="id")
     */
    private $debitNote;
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function generateAccountMovementForAccount($detail, $amount, Account $account, $debitNote){
        parent::generateAccountMovementForAccount($detail, $amount, $account, $debitNote);
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
     * Set debitNote
     *
     * @param \AppBundle\Entity\DebitNote $debitNote
     *
     * @return DebitNoteAccountMovement
     */
    public function setDebitNote(\AppBundle\Entity\DebitNote $debitNote)
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
     * Set document
     *
     * @param \AppBundle\Entity\DebitNote $debitNote
     *
     * @return DebitNoteAccountMovement
     */
    public function setDocument(\AppBundle\Entity\DebitNote $debitNote)
    {
        $this->setDebitNote($debitNote);
    }
    
    /**
     * Get document
     *
     * @return \AppBundle\Entity\DebitNote
     */
    public function getDocument()
    {
        return $this->getDebitNote();
    }
}
