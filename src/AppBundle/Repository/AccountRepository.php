<?php

namespace AppBundle\Repository;

/**
 * AccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountRepository extends \Doctrine\ORM\EntityRepository
{
    public function findMovements()
    {
        
        return $this->getEntityManager()
            ->createQuery(
                "SELECT am FROM AppBundle:InvoiceAccountMovement am"
            )
            ->getResult();
    }
}
