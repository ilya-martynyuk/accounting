<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\Purse;
use AppBackEndBundle\Form\PurseType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyPursesController
 *
 * @package AppBackEndBundle\Controller
 */
class MyPursesController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns all purses of current user"
     * )
     */
    public function getPursesAction()
    {
        $qb = $this->getQueryBuilder()
            ->select('p.balance, p.name, p.id')
            ->from('AppBackEndBundle:Purse', 'p')
            ->where('p.user = :user_id')
            ->setParameter('user_id', $this->getCurrentUser()->getId())
            ->getQuery();

        return $this->handleCollection($qb);
    }

    public function postPurseAction(Request $request)
    {
        $purseName = $request->get('name');

        $purseExist = $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:Purse')
            ->getByNameAndUserId($purseName, $this->getCurrentUser()->getId());

        if ($purseExist) {
            return $this->view([
                'error' => 'Purse already exist.'
            ], Response::HTTP_CONFLICT);
        }

        $purse = new Purse();
        $purseType = new PurseType();

        $this->processForm($purseType, $purse, $request, function($purse){
            $purse->setUser($this->getCurrentUser());
        });

        return $this->view([
            'purse' => $purse
        ], Response::HTTP_CREATED);
    }

    public function putPurseAction($id)
    {

    }

    public function patchPurseAction($id)
    {

    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns current user purse by id"
     * )
     */
    public function getPurseAction($id)
    {
        $purseRepo = $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:Purse');

        $purse = $purseRepo
            ->getByIdAndUserId($id, $this->getCurrentUser()->getId());

        if (!$purse) {
            return $this->view([
                'error' => $this
                    ->get('translator')
                    ->trans("Purse doesn't exist.")
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->view([
            'purse' => $purse
        ], Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns current user purse by id"
     * )
     */
    public function deletePurseAction($id)
    {
        $purseRepo = $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:Purse');

        $deleted = $purseRepo
            ->deleteByIdAndUserId($id, $this->getCurrentUser()->getId());

        if (!$deleted) {
            return $this->view([
                'error' => $this
                    ->get('translator')
                    ->trans("Purse doesn't exist.")
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
