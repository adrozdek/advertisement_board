<?php

namespace BoardBundle\Controller;

use BoardBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    private function generateCategoryForm($category, $action) {
        $form = $this->createFormBuilder($category);
        $form->add('name', 'text', ['trim' => true]);
        $form->add('save', 'submit');
        $form->setAction($action);

        return $form->getForm();
    }

    /**
     * @Route("/createCategory", name = "createCategory")
     * @Template()
     * @Method("GET")
     */
    public function createCategoryAction()
    {
        $category = new Category();
        $categoryForm = $this->generateCategoryForm($category, $this->generateUrl('createCategoryPost'));

        return ['form' => $categoryForm->createView()];
    }

    /**
     * @Route("/createCategory", name = "createCategoryPost")
     * @Template()
     * @Method("POST")
     */
    public function createCategoryPostAction(Request $req)
    {
        $category = new Category();
        $categoryForm = $this->generateCategoryForm($category, $this->generateUrl('createCategoryPost'));
        $categoryForm->handleRequest($req);

        if($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);

            $em->flush();
        }

        return $this->redirectToRoute('showAllCategories');


    }

    /**
     * @Route("/allCategories", name = "showAllCategories" )
     * @Template()
     */
    public function allCategoriesAction()
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Category');
        $categories = $repo->findAll();
        //@TODO: wyszukiwanie alfabetyczne

        return ['categories' => $categories];
    }

    /**
     * @Route("/showCategory/{id}", name = "showCategory")
     * @Template()
     */
    public function showCategoryAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Category');
        $category = $repo->find($id);
        return ['category' => $category];
    }

}
