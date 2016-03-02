<?php

namespace BoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CategoryController extends Controller
{
    /**
     * @Route("/createCategory")
     * @Template()
     */
    public function createCategoryAction()
    {
        return [];
    }

    /**
     * @Route("/allCategories")
     * @Template()
     */
    public function allCategoriesAction()
    {
        return [];
    }

    /**
     * @Route("/showCategory")
     * @Template()
     */
    public function showCategoryAction()
    {
        return [];
    }

}
