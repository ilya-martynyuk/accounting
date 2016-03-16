<?php

namespace AccountingApiBundle\Repository;

/**
 * PursesRepository
 *
 * @codeCoverageIgnore
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PursesRepository extends \Doctrine\ORM\EntityRepository
{
    public function deleteByIdAndUserId($purseId, $userId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user_id')
            ->setParameter('id', $purseId)
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult();
    }

    public function getByIdAndUserId($purseId, $userId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user_id')
            ->setParameter('id', $purseId)
            ->setParameter('user_id', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByNameAndUserId($purseName, $userId)
    {
       return $this->createQueryBuilder('p')
            ->where('p.name = :purse_name')
            ->andWhere('p.user = :user_id')
            ->setParameter('purse_name', $purseName)
            ->setParameter('user_id', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}