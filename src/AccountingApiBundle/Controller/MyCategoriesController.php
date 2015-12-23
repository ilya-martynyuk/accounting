<?php

namespace AccountingApiBundle\Controller;

use AccountingApiBundle\Fields\CategoryFields;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Request;
use AccountingApiBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyCategoriesController
 *
 * @package AccountingApiBundle\Controller
 */
class MyCategoriesController extends BaseController
{
    /**
     * Returns all categories of current user
     *
     * @ApiDoc(
     *      section="Categories",
     *      resource=true,
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *      }
     * )
     *
     * @QueryParam(name="orderBy", description="Order by parameter")
     * @QueryParam(name="order", requirements="(asc|desc)", default="asc", allowBlank=false, description="Order direction parameter")
     * @QueryParam(name="perPage", requirements="\d+", default=100, allowBlank=false, description="Max results amount")
     * @QueryParam(name="page", requirements="\d+", default=1, allowBlank=false, description="Current page number")
     * @QueryParam(name="filters", nullable=true, default=null, description="Filtering rules")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function getCategoriesAction(ParamFetcherInterface $paramFetcher)
    {
        $qb = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findByUserId($this->getCurrentUser()->getId(), true);

        return $this->handleCollection($qb, $paramFetcher);
    }

    /**
     * Returns certain category of current user
     *
     * @ApiDoc(
     *      section="Categories",
     *      requirements={
     *          {
     *              "name"="categoryId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Category id"
     *          }
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Category",
     *          "groups"={"details"}
     *      },
     *      statusCodes={
     *          200="When successful",
     *          403="When the user is not authorized",
     *          404="When the category not found"
     *      }
     * )
     *
     * @param $categoryId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCategoryAction($categoryId)
    {
        $category = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findByIdAndUserId($categoryId, $this->getCurrentUser()->getId());

        return $this->handleGetSingle($category);
    }

    public function postCategoryAction(Request $request)
    {
        $globalCategory = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findBy([
                'name' => $request->request->get('name'),
                'global' => true
            ]);

        if ($globalCategory) {
            return $this->view([
                'reason' => [
                    'name' => $this
                        ->get('translator')
                        ->trans('category.exist_as_global')
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $category = new Category();
        $category->setUser($this->getCurrentUser());

        return $this->processForm($category, ['name']);
    }

    /**
     * Partially or fully update category of current user
     *
     * @ApiDoc(
     *      section="Categories",
     *      input={
     *          "class"="AccountingApiBundle\Entity\Category",
     *          "groups"={"create"}
     *      },
     *      output={
     *          "class"="AccountingApiBundle\Entity\Category",
     *          "groups"={"details"}
     *      },
     *      statusCodes={
     *          200="When successful",
     *          400="When some of required parameters are not presented",
     *          403="When the user is not authorized",
     *          404="When the category not found"
     *      }
     * )
     *
     * @param         $categoryId
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function patchCategoryAction($categoryId, Request $request)
    {
        $category = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findByIdAndUserId($categoryId, $this->getCurrentUser()->getId());

        if ($category && $category->isGlobal()) {
            return $this->errorView('category.is_global', Response::HTTP_FORBIDDEN);
        }

        $globalCategory = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findBy([
                'name' => $request->request->get('name'),
                'global' => true
            ]);

        if ($globalCategory) {
            return $this->view([
                'reason' => [
                    'name' => $this
                        ->get('translator')
                        ->trans('category.exist_as_global')
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->handlePath($category, (new CategoryFields())->getFields());
    }

    /**
     * Delete certain category of current user
     *
     * @ApiDoc(
     *      section="Categories",
     *      requirements={
     *          {
     *              "name"="categoryId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Category id"
     *          }
     *      },
     *      statusCodes={
     *          204="When successful",
     *          403="When the user is not authorized",
     *          404="When the category not found"
     *      }
     * )
     *
     * @param $categoryId
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteCategoryAction($categoryId)
    {
        $category = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findByIdAndUserId($categoryId, $this->getCurrentUser()->getId());

        if ($category && $category->isGlobal()) {
            return $this->errorView('category.is_global', Response::HTTP_FORBIDDEN);
        }

        return $this->handleDelete($category);
    }
}
