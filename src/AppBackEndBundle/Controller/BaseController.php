<?php

namespace AppBackEndBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations;

/**
 * Class BaseController
 *
 * @package AppBackEndBundle\Controller
 */
abstract class BaseController extends FOSRestController
{
    protected function processForm($entity, Request $request, $afterValidateCallback = false)
    {
        $requestMethod =  $request->getMethod();

        $entityForm = $this
            ->get('forms.entity_form')
            ->load($entity)
            ->populate($request->request->all())
            ->validate();

        if (false === $entityForm->isValid()) {
            return $this->errorView($entityForm->getErrors(), Response::HTTP_BAD_REQUEST);
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

    public function handleCollection($qb, ParamFetcherInterface $paramFetcher)
    {
        $orderBy = $paramFetcher->get('orderBy');

        if ($orderBy) {
            $aliases = $qb->getRootAliases();

            if (count($aliases) > 0) {
                $order = 'asc';

                if ($paramFetcher->get('order') === 'desc') {
                    $order = 'desc';
                }

                $alias = $qb->getRootAliases()[0];
                $qb->orderBy($alias . '.' . $orderBy, $order);
            }
        }

        $collection = $qb->getQuery();

        $paginator  = $this->get('knp_paginator');
        $paginated = $paginator->paginate(
            $collection,
            $paramFetcher->get('page'),
            $paramFetcher->get('perPage')
        );

        $extendedData = [
            'data' => $paginated->getItems(),
            '_meta_data' => [
                'total_items' => $paginated->getTotalItemCount(),
                'total_pages' => $paginated->getPageCount(),
                'per_page' => $paramFetcher->get('perPage'),
                'current_page' => $paramFetcher->get('page')
            ]
        ];

        return $this->view($extendedData);
    }

    protected function handleGetSingle($entity)
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        return $this->singleView($entity);
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

    public function handlePath($entity, Request $request)
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        return $this->processForm($entity, $request);
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

    /**
     * Returns entity manager builder
     *
     * @return object
     */
    protected function getManager()
    {
        return $this
            ->getDoctrine()
            ->getManager();
    }

    /**
     * Returns query builder
     *
     * @return object
     */
    protected function getQueryBuilder()
    {
        return $this
            ->getManager()
            ->createQueryBuilder();
    }

    /**
     * Returns current user object
     *
     * @return object
     */
    protected function getCurrentUser()
    {
        return $this
            ->get('security.token_storage')
            ->getToken()
            ->getUser();
    }
}