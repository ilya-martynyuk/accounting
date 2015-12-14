<?php

namespace AppBackEndBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
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

    protected function getCurrentUser()
    {
        return $this
            ->get('security.token_storage')
            ->getToken()
            ->getUser();
    }

    protected function handleInvalidForm(Form $form)
    {
        $errors = $this
            ->get('form.errors_formatter')
            ->getErrors($form);

        return $this->errorView($errors, Response::HTTP_BAD_REQUEST);
    }

    protected function processForm($entityTypeClass, $entity, Request $request, $afterValidateCallback = false)
    {
        $requestMethod =  $request->getMethod();

        $form = $this
            ->createForm($entityTypeClass, $entity);

        $form
            ->submit($request, $requestMethod !== 'PATCH');

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

        return $this->singleView($entity, $responseStatus);
    }

    protected function handleGetSingle($entity)
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        return $this->singleView($entity);
    }

    public function handleCollection($collection)
    {
        if ('Doctrine\ORM\PersistentCollection' !== get_class($collection)) {
            $collection = $collection->getResult();
        }

        return $this->collectionView($collection);
    }

    protected function handleDelete($entity)
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($entity);
        $em->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function handlePath($entity, $entityTypeClass, Request $request)
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        return $this->processForm($entityTypeClass, $entity, $request);
    }

    protected function errorView($errors = null, $statusCode = Response::HTTP_NOT_FOUND)
    {
        $data = [];

        if (is_null($errors)) {
            $errors = Response::$statusTexts[$statusCode];
        }

        $data['reason'] = $errors;

        return $this->view($data, $statusCode);
    }

    protected function collectionView($data = null)
    {
        $extendedData = [
            'data' => $data,
            '_meta_data' => [
                'total_items' => 1000,
                'total_pages' => 100,
                'current_page' => 1
            ]
        ];

        return $this->view($extendedData);
    }

    protected function singleView($data = null, $statusCode = Response::HTTP_OK, array $headers = array())
    {
        $extendedData = [
            'data' => $data
        ];

        return $this->view($extendedData, $statusCode, $headers);
    }

    protected function view($data = null, $statusCode = Response::HTTP_OK, array $headers = array())
    {
        $successStatuses = [
            Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT
        ];

        $success = in_array($statusCode, $successStatuses);
        $status = $success ? 'success' : 'error';

        $extendedData = [
            'status' => $status,
            'code' => $statusCode
        ];

        if (is_array($data)) {
            $extendedData += $data;
        }

        return parent::view($extendedData, $statusCode, $headers);
    }
}