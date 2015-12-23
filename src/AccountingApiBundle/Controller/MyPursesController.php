<?php

namespace AccountingApiBundle\Controller;

use AccountingApiBundle\Entity\Purse;
use AccountingApiBundle\Fields\PurseFields;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Class MyPursesController
 *
 * @package AccountingApiBundle\Controller
 */
class MyPursesController extends BaseController
{
    /**
     * Returns all purses of current user
     *
     * @ApiDoc(
     *      resource=true,
     *      section="Purses",
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *      }
     * )
     *
     * @QueryParam(name="orderBy", description="Order by parameter")
     * @QueryParam(name="order", requirements="(asc|desc)", default="asc", allowBlank=false, description="Order direction parameter")
     * @QueryParam(name="perPage", requirements="\d+", default=100, allowBlank=false, description="Max results amount")
     * @QueryParam(name="page", requirements="\d+", default=1, allowBlank=false, description="Current page number")
     * @QueryParam(name="filters", nullable=true, default=null, description="Filtering rules")
     *
     * @param $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getPursesAction(ParamFetcherInterface $paramFetcher)
    {
        $qb = $this->getQueryBuilder()
            ->select('p.balance, p.name, p.id')
            ->from('AccountingApiBundle:Purse', 'p')
            ->where('p.user = :user_id')
            ->setParameter('user_id', $this->getCurrentUser()->getId());

        return $this->handleCollection($qb, $paramFetcher);
    }

    /**
     * Creates new purse of current user
     *
     * @ApiDoc(
     *      section="Purses",
     *      input={
     *          "class"="AccountingApiBundle\Entity\Purse",
     *          "groups"={"create"}
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Purse",
     *          "groups"={"details"}
     *      },
     *      statusCodes={
     *          200="When successful",
     *          400="When some of required parameters are not presented",
     *          403="When the user is not authorized",
     *      }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function postPurseAction()
    {
        $purse = new Purse();
        $purse->setUser($this->getCurrentUser());

        return $this->processForm($purse, (new PurseFields())->getFields());
    }

    /**
     * Partially or fully update purse of current user
     *
     * @ApiDoc(
     *      section="Purses",
     *      input={
     *          "class"="AccountingApiBundle\Entity\Purse",
     *          "groups"={"create"}
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Purse",
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
     *
     * @return \FOS\RestBundle\View\View
     */
    public function patchPurseAction($purseId)
    {
        $purse = $this->getMyPurseById($purseId);

        return $this->handlePath($purse, (new PurseFields())->getFields());
    }

    /**
     * Returns certain purse of current user
     *
     * @ApiDoc(
     *      section="Purses",
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Purse",
     *          "groups"={"details"}
     *      },
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *          404="When the purse not found"
     *      }
     * )
     *
     * @param $purseId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getPurseAction($purseId)
    {
        $purse = $this->getMyPurseById($purseId);

        return $this->handleGetSingle($purse);
    }

    /**
     * Delete certain purse of current user
     *
     * @ApiDoc(
     *      section="Purses",
     *      requirements={
     *          {
     *              "name"="purseId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Purse id"
     *          }
     *      },
     *      statusCodes={
     *          204="When successful",
     *          403="When the user is not authorized",
     *          404="When the purse not found"
     *      }
     * )
     *
     * @param $purseId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deletePurseAction($purseId)
    {
        $purse = $this->getMyPurseById($purseId);

        return $this->handleDelete($purse);
    }

    /**
     * Returns certain purse of current user.
     * Just a helper for DRY principle.
     *
     * @param $purseId
     *
     * @return mixed
     */
    protected function getMyPurseById($purseId)
    {
        return $this
            ->getDoctrine()
            ->getRepository('AccountingApiBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());
    }
}
