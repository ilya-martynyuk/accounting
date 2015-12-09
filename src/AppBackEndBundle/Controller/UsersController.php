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
    public function getAction()
    {
        $qb = $this->getQueryBuilder()
            ->select('u.id, u.username, u.email')
            ->from('AppBackEndBundle:User', 'u')
            ->getQuery();

        return $this->handleCollection($qb);
    }

    /**
     * @Rest\Get("/me")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns current user profile data"
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getMeAction()
    {
        return $this->view([
            'profile' => $this->getCurrentUser()
        ], Response::HTTP_OK);
    }
}
