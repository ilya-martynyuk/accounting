<?php

namespace AppBackEndBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

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

    public function handleCollection($collection)
    {
        if ('Doctrine\ORM\PersistentCollection' !== get_class($collection)) {
            $collection = $collection->getResult();
        }

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
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function getCurrentUser()
    {
        return $this
            ->get('security.context')
            ->getToken()
            ->getUser();
    }

    protected function processForm($entityType, $entity, $request, $afterValidateCallback = false)
    {
        $form = $this->createForm($entityType, $entity);
        $form->submit($request);

        // Wrong credentials.
        if (false === $form->isValid()) {
            return $this->handleInvalidForm($form);
        }
        $em = $this
            ->getDoctrine()
            ->getManager();

        if (is_callable($afterValidateCallback)) {
            $afterValidateCallback($entity);
        }

        $em->persist($entity);
        $em->flush();
    }
}