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
     * @ApiDoc(
     *  resource=true,
     *  description="Returns list of users"
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
     * @ApiDoc(
     *  resource=true,
     *  description="Returns current user profile data"
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getUsersMeAction()
    {
        return $this->singleView($this->getCurrentUser());
    }
}
