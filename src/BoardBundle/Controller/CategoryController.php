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
        date_default_timezone_set("Europe/Warsaw");
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $ads = $repo->findAdsinCategoryOBCreationDate($category, $dateNow);

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
        date_default_timezone_set("Europe/Warsaw");
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $ads = $repo->findAdsinCategoryOBExpirationDate($category, $dateNow);

        return ['category' => $category, 'ads' => $ads];
    }

    /**
     * @Route("/admin/removeCategory/{id}", name = "removeCategory")
     *
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
