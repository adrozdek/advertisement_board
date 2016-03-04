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
     *
     */
    public function allCategoriesAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT c FROM BoardBundle:Category c ORDER BY c.name ASC'
        );

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );

        // parameters to template
        return $this->render('BoardBundle:Category:allCategories.html.twig', array('pagination' => $pagination));

    }

    /**
     * @Route("/showCategory/{id}", name = "showCategory")
     *
     */
    public function showCategoryAction(Request $request, $id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Category');
        $category = $repo->find($id);
        date_default_timezone_set("Europe/Warsaw");
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));

        $em    = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT a FROM BoardBundle:Ad a JOIN a.categories t WHERE t = :category AND a.expirationDate > :dateNow ORDER BY a.creationDate DESC'
        )->setParameter('category', $category)->setParameter('dateNow', $dateNow);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        // parameters to template
        return $this->render('BoardBundle:Category:showCategory.html.twig', array('category' => $category, 'pagination' => $pagination));

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
