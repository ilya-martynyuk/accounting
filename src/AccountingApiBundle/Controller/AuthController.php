<?php

namespace AccountingApiBundle\Controller;

use AccountingApiBundle\Entity\User;
use AccountingApiBundle\Form\UserType;
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
     *      section="Auth",
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

        $entityForm= $this
            ->get('forms.entity_form')
            ->load($user)
            ->populate($request->request->all(), ['username', 'password'])
            ->validate();

        if (false === $entityForm->isValid()) {
            return $this->errorView($entityForm->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this
            ->getDoctrine()
            ->getRepository('AccountingApiBundle:User')
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
