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
class UsersController extends BaseController
{
    /**
     * Returns list of users
     *
     * @ApiDoc(
     *      section="Purses",
     *      resource=true,
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
     * @param ParamFetcherInterface $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function getUsersAction(ParamFetcherInterface $paramFetcher)
    {
        $qb = $this->getQueryBuilder()
            ->select('u.id, u.username, u.email')
            ->from('AccountingApiBundle:User', 'u');

        return $this->handleCollection($qb, $paramFetcher);
    }

    /**
     * Returns current user profile data
     *
     * @ApiDoc(
     *      section="Users",
     *      resource=true,
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *      }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getUsersMeAction()
    {
        return $this->singleView($this->getCurrentUser());
    }
}
