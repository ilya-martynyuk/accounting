<?php
/**
 * Created by PhpStorm.
 * User: imartynyuk
 * Date: 15.12.15
 * Time: 15:43
 */

namespace AppBackEndBundle\Controller;

use AppBackEndBundle\Entity\Operation;
use AppBackEndBundle\Form\OperationType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyCategories
 *
 * @package AppBackEndBundle\Controller
 */
class MyCategoriesController extends BaseController
{
    public function getCategoriesAction()
    {
        $qb = $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Category')
            ->findByUserId($this->getCurrentUser()->getId(), true);

        return $this->handleCollection($qb);
    }

    public function getCategoryAction($categoryId)
    {
        $category = $this
            ->getManager()
            ->getRepository('AppBackEndBundle:Category')
            ->findByIdAndUserId($categoryId, $this->getCurrentUser()->getId());

        return $this->handleGetSingle($category);
    }
}
