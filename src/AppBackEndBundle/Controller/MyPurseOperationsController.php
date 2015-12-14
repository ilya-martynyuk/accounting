<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\Operation;
use AppBackEndBundle\Form\OperationType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $purse = $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());

        if (!$purse) {
            return $this->errorView('purse.not_found');
        }

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
        $purse = $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());

        if (!$purse) {
            return $this->errorView('purse.not_found');
        }

        $operation = new Operation();
        $operation->setPurse($purse);

        return $this->processForm(OperationType::class, $operation, $request, function($operation) use ($purse) {
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
     * @return \FOS\RestBundle\View\View
     * @throws \Exception
     */
    public function deleteOperationAction($purseId, $operationId)
    {
        $operation = $this->findMyOperationByPurse($operationId, $purseId);

        if (!$operation) {
            return $this->errorView("entity.not_found");
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em
            ->getConnection()
            ->beginTransaction();

        try {
            $purse = $operation->getPurse();
            $purse->removeOperation($operation);

            $em->remove($operation);
            $em->flush();
            $em->getConnection()->commit();

            return $this->view(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    public function patchOperationAction($purseId, $operationId, Request $request)
    {
        $operation = $this->findMyOperationByPurse($operationId, $purseId);

        if (!$operation) {
            return $this->errorView("entity.not_found");
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em
            ->getConnection()
            ->beginTransaction();

        try {
            $oldOperation = clone $operation;

            $result = $this->processForm(OperationType::class, $operation, $request, function($operation) use ($oldOperation){
                $purse = $operation->getPurse();
                $purse->removeOperation($oldOperation);
                $purse->processOperation($operation);
            });

            $em->getConnection()->commit();

            return $result;
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
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
