<?php
/**
 * Created by PhpStorm.
 * User: imartynyuk
 * Date: 28.12.15
 * Time: 13:59
 */

namespace AccountingApiBundle\Services;

use Symfony\Component\DependencyInjection\Exception\LogicException;

class RequestFiltering
{
    /**
     * Equivalent expression (=)
     */
    const EXPR_EQ = 'eq';

    public function getRootAlias($qb)
    {
        $rootAlias = '';
        $aliases = $qb->getRootAliases();

        if (count($aliases) > 0) {
            $rootAlias = $qb->getRootAliases()[0] . '.';
        }

        return $rootAlias;
    }

    /**
     * Applies filter for query
     *
     * @param $qb Query builder object
     * @param $filterConfig Filter configuration
     *
     * @return mixed
     */
    public function applyFilter($qb, $filterConfig)
    {
        $rootAlias = $this->getRootAlias($qb);

        try {
            list($field, $expression, $value) = explode(',', $filterConfig);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException();
        }

        $expression = strtolower($expression);

        $expr = $qb
            ->expr();

        switch ($expression) {
            case self::EXPR_EQ:
                $expr = $expr
                    ->eq($rootAlias . $field, $value);
                break;
            default:
                throw new \InvalidArgumentException();
        }

        $qb->andWhere($expr);

        return $qb;
    }

    public function applyFilters($qb, $filters)
    {
        if (is_array($filters)) {
            foreach ($filters as $filterConfig) {
                $qb = $this->applyFilter($qb, $filterConfig);
            }
        }

        return $qb;
    }
}
