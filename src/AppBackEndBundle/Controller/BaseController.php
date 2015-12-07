<?php

namespace AppBackEndBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Form;

/**
 * Class BaseController
 *
 * @package AppBackEndBundle\Controller
 */
abstract class BaseController extends FOSRestController
{
    protected function getQueryBuilder()
    {
        return $this
            ->getDoctrine()
            ->getManager()
            ->createQueryBuilder();
    }

    public function handleCollection($qb)
    {
        $collection = $qb->getResult();

        return $this->view([
            'collection' => $collection
        ]);
    }

    /**
     * Handles invalid form.
     *
     * @param Form $form
     * @return \FOS\RestBundle\View\View
     */
    protected function handleInvalidForm(Form $form)
    {
        $errors = $this
            ->get('form.errors_formatter')
            ->getErrors($form);

        return $this->view([
            'errors' => $errors
        ], 400);
    }
}