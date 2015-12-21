<?php

namespace AccountingApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Request;
use AccountingApiBundle\Entity\Category;
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
    public function getCategoryAction($categoryId, Request $request)
    {
        $category = $this
            ->getManager()
            ->getRepository('AccountingApiBundle:Category')
            ->findByIdAndUserId($categoryId, $this->getCurrentUser()->getId());

        return $this->handleGetSingle($category);
    }

    public function postCategoryAction()
    {
        $category = new Category();
        $category->setUser($this->getCurrentUser());

        return $this->processForm($category, ['name']);
    }
}
