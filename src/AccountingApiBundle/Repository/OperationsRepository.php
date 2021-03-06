<?php

namespace AccountingApiBundle\Repository;

/**
 * OperationsRepository
 *
 * @codeCoverageIgnore
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperationsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Returns all operations which tied to certain purse and user
     *
     * @param            $purseId
     * @param            $userId
     * @param bool|false $returnQuery Flag which means that only query object should be returned instead of result
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function findByPurseIdAndUserId($purseId, $userId, $returnQuery = false)
    {
        $query = $this
            ->createQueryBuilder('op')
            ->leftJoin('AccountingApiBundle:Purse', 'p', 'WITH', 'p.id=op.purse')
            ->leftJoin('AccountingApiBundle:User', 'u', 'WITH', 'u.id=p.user')
            ->where('p.id=:purse_id')
            ->andWhere('u.id=:user_id')
            ->setParameter('purse_id', $purseId)
            ->setParameter('user_id', $userId);

        if ($returnQuery) {
            return $query;
        }

        return $query
            ->qetQuery()
            ->getResult();
    }

    /**
     * Returns all operations which tied to certain user
     *
     * @param            $userId
     * @param bool|false $returnQuery Flag which means that only query object should be returned instead of result
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function findByUserId($userId, $returnQuery = false)
    {
        $query = $this
            ->createQueryBuilder('op')
            ->leftJoin('AccountingApiBundle:Purse', 'p', 'WITH', 'p.id=op.purse')
            ->leftJoin('AccountingApiBundle:User', 'u', 'WITH', 'u.id=p.user')
            ->andWhere('u.id=:user_id')
            ->setParameter('user_id', $userId);

        if ($returnQuery) {
            return $query;
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns concrete operation tied to certain purse and user
     *
     * @param $operationId
     * @param $purseId
     * @param $userId
     *
     * @return bool
     */
    public function findOneByIdAndPurseIdAndUserId($operationId, $purseId, $userId)
    {
        return $this
            ->createQueryBuilder('op')
            ->leftJoin('AccountingApiBundle:Purse', 'p', 'WITH', 'p.id=op.purse')
            ->where('op.id=:operation_id')
            ->andWhere('p.user=:user_id')
            ->andWhere('p.id=:purse_id')
            ->setParameter('user_id', $userId)
            ->setParameter('operation_id', $operationId)
            ->setParameter('purse_id', $purseId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
