<?php

namespace AccountingApiBundle\Controller;

use Doctrine\ORM\Query\QueryException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class BaseController
 *
 * Contains base functional for all API controllers
 *
 * @package AccountingApiBundle\Controller
 */
abstract class BaseController extends FOSRestController
{
    /**
     * Handling creating and updating of entity and invokes an API response
     *
     * @param            $entity
     * @param array      $allowFields
     * @param bool|false $afterValidateCallback
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function processForm($entity, array $allowFields = [], $afterValidateCallback = false)
    {
        $request = $this->container->get('request');
        $requestMethod =  $request->getMethod();

        $entityForm = $this
            ->get('forms.entity_form')
            ->load($entity)
            ->populate($request->request->all(), $allowFields)
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

    /**
     * Handling request collection of entities and invokes an API response
     *
     * @param                       $qb Query builder prepared query
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     */
    public function handleCollection($qb, ParamFetcherInterface $paramFetcher)
    {
        $paginator  = $this->get('knp_paginator');
        $requestFiltering = $this->get('request_filtering');

        $filters = $paramFetcher->get('filters');

        $orderBy = $paramFetcher->get('orderBy');
        $rootAlias = $requestFiltering->getRootAlias($qb);

        if ($orderBy) {
            $qb->orderBy($rootAlias . $orderBy, $paramFetcher->get('order'));
        }

        try {
            $qb = $requestFiltering->applyFilters($qb, $filters);
        } catch (\InvalidArgumentException $e) {
            return $this->errorView('error.bad_filter_syntax', Response::HTTP_BAD_REQUEST);
        }

        $collection = $qb->getQuery();

        try {
            $paginated = $paginator->paginate(
                $collection,
                $paramFetcher->get('page'),
                $paramFetcher->get('perPage')
            );

        } catch (QueryException $e) {
            return $this->errorView('error.bad_filter_syntax', Response::HTTP_BAD_REQUEST);
        }

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

    /**
     * Handling request single entity and invokes an API response
     *
     * @param $entity Entity which will be returned
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function handleGetSingle($entity)
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        return $this->singleView($entity);
    }

    /**
     * Handling delete requests and invokes an API response for this method
     *
     * @param       $entity Entity which will be deleted
     *
     * @return \FOS\RestBundle\View\View
     */
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

    /**
     * Handling patch requests and invokes an API response for this method
     *
     * @param       $entity Entity which will be patched
     * @param array $fieldsList An array of fields which allowed to modify
     *
     * @return \FOS\RestBundle\View\View
     */
    public function handlePath($entity, $fieldsList = [])
    {
        if (!$entity) {
            return $this->errorView("entity.not_found");
        }

        return $this->processForm($entity, $fieldsList);
    }

    /**
     * Invokes an API response in case of error
     *
     * @param null|string|array $errors Information about errors which occurred
     * @param int               $statusCode Response status code
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function errorView($errors = null, $statusCode = Response::HTTP_NOT_FOUND)
    {
        $data = [];

        if (is_null($errors)) {
            $errors = Response::$statusTexts[$statusCode];
        }

        $data['reason'] = $errors;

        return $this->view($data, $statusCode);
    }

    /**
     * Invokes an API response for single entity representation
     *
     * @param null  $data Response data
     * @param int|null $statusCode Response status code
     * @param array    $headers Response headers
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function singleView($data = null, $statusCode = Response::HTTP_OK, array $headers = array())
    {
        $extendedData = [
            'data' => $data
        ];

        return $this->view($extendedData, $statusCode, $headers);
    }

    /**
     * Invokes an API response with necessary status code and data
     *
     * @param null     $data Response data
     * @param int|null $statusCode Response status code
     * @param array    $headers Response headers
     *
     * @return \FOS\RestBundle\View\View
     */
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