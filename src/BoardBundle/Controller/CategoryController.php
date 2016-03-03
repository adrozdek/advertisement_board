<?php

namespace BoardBundle\Controller;

use BoardBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/admin/createCategory", name = "createCategory")
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
     * @Route("/admin/createCategory", name = "createCategoryPost")
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
        $categories = $repo->findCategoriesOrderByName();

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
        $ads = $repo->findAdsinCategoryOBCreationDate($category);

        //@TODO: pokaż po expirationDate
        return ['category' => $category, 'ads' => $ads];
    }

    /**
     * @Route("/showCategoryExp/{id}", name = "showCategoryExpiration")
     * @Template("BoardBundle:Category:showCategoryExp.html.twig")
     */
    public function showCategoryByExpirationAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Category');
        $category = $repo->find($id);
        $ads = $repo->findAdsinCategoryOBExpirationDate($category);

        return ['category' => $category, 'ads' => $ads];
    }

    /**
     * @Route("/admin/removeCategory/{id}", name = "removeCategory")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeCategoryAction($id) {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Category');
        $category = $repo->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('showAllCategories');
    }

}
