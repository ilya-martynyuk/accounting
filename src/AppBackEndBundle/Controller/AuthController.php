<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\User;
use AppBackEndBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    /**
     * @Rest\Post("/login")
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function postLoginAction(Request $request)
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
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:User')
            ->findOneByUsername($user->getUsername());

        // User isn't exist.
        if (null === $user) {
            return $this->view([
                'errors' => [
                    'username' => $this
                        ->get('translator')
                        ->trans('Invalid username or password.')
                ]
            ], Response::HTTP_UNAUTHORIZED);
        }

        $accessToken = $this
            ->get('security.jwt')
            ->generate([
                'username' => $user->getUsername()
            ]);

        return $this->view([
            'access_token' => $accessToken
        ], Response::HTTP_OK);
    }
}
