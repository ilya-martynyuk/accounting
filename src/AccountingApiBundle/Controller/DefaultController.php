<?php

namespace AccountingApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends BaseController
{
    /**
     * @Rest\Get("/", name="_api")
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(Request $request)
    {
        return $this->view([
            'api_doc' => $this->generateUrl('nelmio_api_doc_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'about' => $this->container->getParameter('accounting_api_about'),
            //'support_address' => $this->container->getParameter('accounting_api.support_address')
        ], Response::HTTP_OK);
    }
}