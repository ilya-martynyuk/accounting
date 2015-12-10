<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\Operation;
use AppBackEndBundle\Form\OperationType;
use FOS\RestBundle\Controller\Annotations as Rest;

class MyPurseOperationsController extends BaseController
{
    public function getOperationsAction($purseId)
    {
        $qb = $this->getQueryBuilder()
            ->select('op.id, op.amount, op.direction')
            ->from('AppBackEndBundle:Operation', 'op')
            ->andWhere('op.purse=:purse_id')
            ->setParameter('purse_id', $purseId)
            ->getQuery();

        return $this->handleCollection($qb);
    }

    public function postOperationAction($operationId)
    {

    }

    public function getOperationAction($purseId, $operationId)
    {
        $purse = $this->getMyPurseOperationById($operationId);

        return $this->handleGetSingle($purse, 'operation');
    }

    public function deleteOperationAction($purseId, $operationId)
    {

    }

    public function patchOperationAction($purseId, $operationId)
    {

    }

    protected function getMyPurseOperationById($operationId)
    {
        $logger = new \Doctrine\DBAL\Logging\DebugStack();
         $this->container
            ->get('doctrine')
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger($logger);


        $query = $this->getQueryBuilder()
            ->select('op.id, op.amount, op.direction')
            ->from('AppBackEndBundle:Operation', 'op')
            ->leftJoin('AppBackEndBundle:Purse', 'p')
            ->where('p.user=:user_id')
            ->andWhere('op.id=:operation_id')
            ->setParameter('user_id', $this->getCurrentUser()->getId())
            ->setParameter('operation_id', $operationId)
            ->getQuery()            ->getResult();

        var_dump($logger->queries);;exit;

        return $this->getQueryBuilder()
            ->select('op.id, op.amount, op.direction')
            ->from('AppBackEndBundle:Operation', 'op')
            ->leftJoin('AppBackEndBundle:Purse', 'p')
            ->where('p.user=:user_id')
            ->andWhere('op.id=:operation_id')
            ->setParameter('user_id', $this->getCurrentUser()->getId())
            ->setParameter('operation_id', $operationId)
            ->getQuery()
            ->getResult();
    }
}
