<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\User;
use AppBackEndBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AuthController extends BaseController
{
    /**
     * Used for authorization and receiving an access_token
     *
     * @Rest\Post("/login")
     *
     * @ApiDoc(
     *      parameters={
     *          {
     *              "name"="username",
     *              "dataType"="string",
     *              "required"=true,
     *              "description"="User name"
     *          }, {
     *              "name"="password",
     *              "dataType"="string",
     *              "required"=true,
     *              "description"="User password"
     *          }
     *      },
     *      statusCodes={
     *          200="When successful",
     *          400="When some of required parameters are not presented",
     *          401="When the username or password is invalid",
     *          403="When the user is not authorized",
     *      }
     * )
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function postLoginAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request);

        if (false === $form->isValid()) {
            return $this->handleInvalidForm($form);
        }

        $user = $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:User')
            ->findOneByUsername($user->getUsername());

        // Username or password is wrong
        if (null === $user) {
            return $this->view([
                'reason' => [
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
            ], $this->container->getParameter('jwt_expiration_time'));

        return $this->view([
            'access_token' => $accessToken
        ], Response::HTTP_OK);
    }
}
