<?php

namespace AppBackEndBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class UserController
 *
 * @package AppBackEndBundle\Controller
 */
class UserController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns list of users"
     * )
     */
    public function getUsersAction()
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:User');

        $qb = $repository
            ->createQueryBuilder('u')
            ->getQuery();

        $users = $qb->getResult();

        return $this->view([
            'users' => $users
        ]);
    }
}