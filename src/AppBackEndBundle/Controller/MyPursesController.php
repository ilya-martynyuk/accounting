<?php

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\Purse;
use AppBackEndBundle\Form\PurseType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

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
        $purse = new Purse();
        $purseType = new PurseType();
        $purse->setUser($this->getCurrentUser());

        return $this->processForm($purseType, $purse, $request);
    }

    public function patchPurseAction($purseId, Request $request)
    {
        $purse = $this->getMyPurseById($purseId);
        $purseType = new PurseType();

        return $this->handlePath($purse, $purseType, $request);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns current user purse by id"
     * )
     */
    public function getPurseAction($purseId)
    {
        $purse = $this->getMyPurseById($purseId);

        return $this->handleGetSingle($purse);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns current user purse by id"
     * )
     */
    public function deletePurseAction($purseId)
    {
        $purse = $this->getMyPurseById($purseId);

        return $this->handleDelete($purse);
    }

    protected function getMyPurseById($purseId)
    {
        return $this
            ->getDoctrine()
            ->getRepository('AppBackEndBundle:Purse')
            ->getByIdAndUserId($purseId, $this->getCurrentUser()->getId());
    }
}
