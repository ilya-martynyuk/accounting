<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\Operation;
use AppBackEndBundle\Form\OperationType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MyPurseOperationsController
 *
 * Responsible for routes which tied to operations on current user certain purse
 *
 * @package AppBackEndBundle\Controller
 */
class MyPurseOperationsController extends BaseController
{
    /**
     * @param $purseId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getOperationsAction($purseId)
    {
        $qb = $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Operation')
            ->findByPurseIdAndUserId($purseId, $this->getCurrentUser()->getId(), true);

        return $this->handleCollection($qb);
    }

    /**
     * @param         $purseId
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function postOperationAction($purseId, Request $request)
    {
        $operation = new Operation();
        $operationType = new OperationType();

        $purse = $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());

        if (!$purse) {
            return $this->errorView('purse.not_found');
        }

        $operation->setPurse($purse);

        return $this->processForm($operationType, $operation, $request, function($operation) use ($purse) {
            $purse->processOperation($operation);
        });
    }

    /**
     * @param $purseId
     * @param $operationId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getOperationAction($purseId, $operationId)
    {
        $operation = $this->findMyOperationByPurse($operationId, $purseId);

        return $this->handleGetSingle($operation);
    }

    /**
     * @param $purseId
     * @param $operationId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteOperationAction($purseId, $operationId)
    {
        $operation = $this->findMyOperationByPurse($operationId, $purseId);

        return $this->handleDelete($operation);
    }

    /**
     * @param         $purseId
     * @param         $operationId
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function patchOperationAction($purseId, $operationId, Request $request)
    {
        $operation = $this->findMyOperationByPurse($operationId, $purseId);
        $operationType = new OperationType();

        return $this->handlePath($operation, $operationType, $request);
    }

    /**
     * Returns certain operation from current user purse
     *
     * @param $operationId
     * @param $purseId
     *
     * @return mixed
     */
    protected function findMyOperationByPurse($operationId, $purseId)
    {
        return $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Operation')
            ->findOneByIdAndPurseIdAndUserId($operationId, $purseId, $this->getCurrentUser()->getId());
    }
}
