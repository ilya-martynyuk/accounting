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
    protected function getManager()
    {
        return $this
            ->getDoctrine()
            ->getManager();
    }

    protected function getQueryBuilder()
    {
        return $this
            ->getManager()
            ->createQueryBuilder();
    }

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

    protected function processForm($entityType, $entity, $request, $entityName = null, $afterValidateCallback = false)
    {
        $requestMethod =  $request->getMethod();

        $form = $this->createForm($entityType, $entity);
        $form->submit($request, $requestMethod !== 'PATCH');

        if (false === $form->isValid()) {
            return $this->handleInvalidForm($form);
        }

        $em = $this
            ->getManager();

        if (is_callable($afterValidateCallback)) {
            $afterValidateCallback($entity);
        }

        $em->persist($entity);
        $em->flush();

        $responseStatus = Response::HTTP_OK;

        if ($requestMethod === 'POST') {
            $responseStatus = Response::HTTP_CREATED;
        }

        return $this->singleView($entity, $entityName, $responseStatus);
    }

    protected function errorView($errorMessage, $statusCode = Response::HTTP_NOT_FOUND)
    {
        return $this->view([
            'error' => $this
                ->get('translator')
                ->trans($errorMessage)
        ], $statusCode);
    }

    protected function singleView($data, $entityName = null,  $statusCode = Response::HTTP_OK)
    {

        if (null === $entityName) {
            $entityName = 'entity';
        }

        return $this->view([
            $entityName => $data
        ], $statusCode);
    }


    protected function handleGetSingle($entity, $entityName = null)
    {
        if (!$entity) {
            return $this->errorView("The entity you are looking for was not found");
        }

        return $this->singleView($entity, $entityName);
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

    protected function handleDelete($entity)
    {
        if (!$entity) {
            return $this->errorView("The entity you are looking for was not found");
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($entity);
        $em->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function handlePath($entity, $entityType, Request $request, $entityName = null)
    {
        if (!$entity) {
            return $this->errorView("The entity you are looking for was not found");
        }

        return $this->processForm($entityType, $entity, $request, $entityName);
    }
}