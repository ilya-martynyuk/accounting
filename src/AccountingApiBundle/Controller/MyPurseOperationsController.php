<?php

namespace AccountingApiBundle\Controller;

use AccountingApiBundle\Entity\Operation;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Class MyPurseOperationsController
 *
 * Responsible for routes which tied to operations on current user certain purse
 *
 * @package AccountingApiBundle\Controller
 */
class MyPurseOperationsController extends BaseController
{
    /**
     * Returns all operations of current user's purse
     *
     * @ApiDoc(
     *      section="Operations",
     *      resource=true,
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }
     *      },
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *          404="When the purse not found"
     *      }
     * )
     *
     * @QueryParam(name="orderBy", default="date", description="Order by parameter")
     * @QueryParam(name="order", requirements="(asc|desc)", default="desc", allowBlank=false, description="Order direction parameter")
     * @QueryParam(name="perPage", requirements="\d+", default=100, allowBlank=false, description="Max results amount")
     * @QueryParam(name="page", requirements="\d+", default=1, allowBlank=false, description="Current page number")
     * @QueryParam(name="filters", nullable=true, default=null, description="Filtering rules")
     *
     * @param $paramFetcher
     *
     * @param $purseId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getOperationsAction($purseId, ParamFetcherInterface $paramFetcher)
    {
        $purse = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());

        if (!$purse) {
            return $this->errorView('purse.not_found');
        }

        $qb = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Operation')
            ->findByPurseIdAndUserId($purseId, $this->getCurrentUser()->getId(), true);

        return $this->handleCollection($qb, $paramFetcher);
    }

    /**
     * Create operation of current user's purse
     *
     * @ApiDoc(
     *      section="Operations",
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }
     *      },
     *      input={
     *          "class"="AccountingApiBundle\Entity\Operation",
     *          "groups"={"create"}
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Operation",
     *          "groups"={"details"}
     *      },
     *      statusCodes={
     *          200="When successful",
     *          400="When some of required parameters are not presented",
     *          403="When the user is not authorized",
     *          404="When the purse not found"
     *      }
     * )
     *
     * @param         $purseId
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function postOperationAction($purseId, Request $request)
    {
        $purse = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());

        if (!$purse) {
            return $this->errorView('purse.not_found');
        }

        $operation = new Operation();
        $operation->setPurse($purse);

        return $this->processForm($operation, $request, function($operation) use ($purse) {
            $purse->processOperation($operation);
        });
    }

    /**
     * Returns certain operation of current user's purse
     *
     * @ApiDoc(
     *      section="Operations",
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }, {
     *              "name"="operationId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Operation id"
     *          }
     *      },
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *          404={
     *              "When the purse not found",
     *              "When the operation not found"
     *          }
     *      }
     * )
     *
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
     * Delete certain operation of current user's purse
     *
     * @ApiDoc(
     *      section="Operations",
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }, {
     *              "name"="operationId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Operation id"
     *          }
     *      },
     *      statusCodes={
     *          204="When successful",
     *          403="When the user is not authorized",
     *          404={
     *              "When the purse not found",
     *              "When the operation not found"
     *          }
     *      }
     * )
     *
     * @param $purseId
     * @param $operationId
     *
     * @return \FOS\RestBundle\View\View
     *
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

    /**
     * Partially or fully update operation of current user's purse
     *
     * @ApiDoc(
     *      section="Operations",
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }, {
     *              "name"="operationId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Operation id"
     *          }
     *      },
     *      input={
     *          "class"="AccountingApiBundle\Entity\Operation",
     *          "groups"={"create"}
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Operation",
     *          "groups"={"details"}
     *      },
     *      statusCodes={
     *          200="When successful",
     *          400="When some of required parameters are not presented",
     *          403="When the user is not authorized",
     *          404={
     *              "When the purse not found",
     *              "When the operation not found"
     *          }
     *      }
     * )
     *
     * @param         $purseId
     * @param         $operationId
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws \Exception
     */
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

            $result = $this->processForm($operation, $request, function($operation) use ($oldOperation){
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
     * Returns certain operation of current user's purse.
     * Just a helper for DRY principle.
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
            ->getRepository('AccountingApiBundle:Operation')
            ->findOneByIdAndPurseIdAndUserId($operationId, $purseId, $this->getCurrentUser()->getId());
    }
}
