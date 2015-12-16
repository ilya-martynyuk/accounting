<?php

namespace AppBackEndBundle\Repository;

/**
 * CategoriesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriesRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByUserId($userId, $returnQuery = false)
    {
        $qb = $this
            ->createQueryBuilder('cat');

        $query = $qb
            ->leftJoin('cat.users', 'cat_users')
            ->where('cat_users.id=:user_id')
            ->orWhere($qb->expr()->eq('cat.global', $qb->expr()->literal(true)))
            ->setParameter('user_id', $userId)
            ->getQuery();

        if ($returnQuery) {
            return $query;
        }

        return $query->getResult();
    }

    public function findByIdAndUserId($categoryId, $userId)
    {
        $qb = $this
            ->createQueryBuilder('cat');

        $category = $qb
            ->leftJoin('cat.users', 'cat_users')
            ->where('cat.id=:category_id')
            ->andWhere(
                $qb
                    ->expr()
                    ->orX()
                    ->add(
                        $qb->expr()->eq('cat.global', $qb->expr()->literal(true))
                    )
                    ->add(
                        $qb->expr()->eq('cat_users.id', ':user_id')
                    )
            )
            ->setParameter('user_id', $userId)
            ->setParameter('category_id', $categoryId)
            ->getQuery()
            ->getOneOrNullResult();

        return $category;
    }
}
