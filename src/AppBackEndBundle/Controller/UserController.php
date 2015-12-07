<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\AccessToken;
use AppBackEndBundle\Entity\User;
use AppBackEndBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * @package AppBackEndBundle\Controller
 */
class UserController extends BaseController
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
            ->select('u.id, u.username')
            ->from('AppBackEndBundle:User', 'u')
            ->getQuery();

        return $this->handleCollection($qb);
    }

    /**
     * @Rest\Post("/users/login")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Login user action"
     * )
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function postUsersLoginAction(Request $request)
    {
        $user = new User();
        $userType = new UserType();

        $form = $this->createForm($userType, $user);
        $form->submit($request);

        // Wrong credentials.
        if (false === $form->isValid()) {
            return $this->handleInvalidForm($form);
        }

        $user = $this
            ->getRepository()
            ->findOneByUsername($user->getUsername());

        // User isn't exist.
        if (null === $user) {
            return $this->view([
                'errors' => [
                    'username' => $this->get('translator')->trans('Invalid username or password.')
                ]
            ], 401);
        }

        $accessToken = $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:AccessToken')
            ->findOneByUserId($user->getId());

        if (null === $accessToken || $accessToken->isExpired()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $newToken = md5(uniqid($user->getUsername(), true));

            $accessToken = new AccessToken();
            $accessToken->setUserId($user->getId());
            $accessToken->setToken($newToken);
            $accessToken->setExpiredAt(new \DateTime(date("Y-m-d H:i:s", time() + 60 * 60)));

            $em->persist($accessToken);
            $em->flush();
        }

        return $this->view([
            'me' => $user,
            'access_token' => $accessToken->getToken(),
            'expired_at' => $accessToken->getExpiredAt()->format("Y-m-d H:i:s")
        ], 200);
    }

    /**
     * Returns User repository.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:User');
    }
}
