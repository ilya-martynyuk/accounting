<?php

namespace AppBackEndBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package AppBackEndBundle\Controller
 */
class UsersController extends BaseController
{
    /**
     * Returns list of users
     *
     * @ApiDoc(
     *      resource=true,
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *      }
     * )
     */
    public function getUsersAction()
    {
        $qb = $this->getQueryBuilder()
            ->select('u.id, u.username, u.email')
            ->from('AppBackEndBundle:User', 'u')
            ->getQuery();

        return $this->handleCollection($qb);
    }

    /**
     * Returns current user profile data
     *
     * @ApiDoc(
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
