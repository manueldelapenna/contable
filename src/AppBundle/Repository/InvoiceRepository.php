<?php

namespace AppBundle\Repository;

/**
 * InvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByOrderId($orderId)
    {
        return $this->getEntityManager()
        ->createQuery(
            "SELECT i FROM AppBundle:Invoice i where i.order = :value"
        )
        ->setParameter('value', $orderId)->getFirstResult();
    }
}
