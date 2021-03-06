<?php

namespace AccountingApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Class UserController
 *
 * @package AccountingApiBundle\Controller
 */
class MyOperationsController extends BaseController
{
    /**
     * Returns list of users
     *
     * @ApiDoc(
     *      section="Operations",
     *      resource=true,
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *      }
     * )
     *
     * @QueryParam(name="orderBy", default="date", description="Order by parameter")
     * @QueryParam(name="order", requirements="(asc|desc)", default="desc", allowBlank=false, description="Order direction parameter")
     * @QueryParam(name="perPage", requirements="\d+", default=100, allowBlank=false, description="Max results amount")
     * @QueryParam(name="page", requirements="\d+", default=1, allowBlank=false, description="Current page number")
     * @QueryParam(name="filters", nullable=true, default=null, description="Filtering rules")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function getOperationsAction(ParamFetcherInterface $paramFetcher)
    {
        $qb = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Operation')
            ->findByUserId($this->getCurrentUser()->getId(), true);

        return $this->handleCollection($qb, $paramFetcher);
    }
}
