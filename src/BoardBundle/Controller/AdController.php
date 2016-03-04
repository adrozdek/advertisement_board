<?php

namespace BoardBundle\Controller;

use BoardBundle\Entity\Ad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;


class AdController extends Controller
{
    private function generateAdForm($ad, $action)
    {
        $form = $this->createFormBuilder($ad);
        $form->add('title', 'text', ['trim' => true]);
        $form->add('description', 'textarea', ['trim' => true]);
        $form->add('photoPath', 'file', ['data_class' => null, 'required' => false]);
        $form->add('expirationDate', 'datetime',
            ['years' => range(date('Y') + 1, date('Y'))]);
        $form->add('categories', 'entity', [
            'class' => 'BoardBundle\Entity\Category',
            'property' => 'name',
            'required' => false,
            'multiple' => true,
            'expanded' => true]);
        $form->setAction($action);

        $adFrom = $form->getForm();

        return $adFrom;
    }

    /**
     * @Route("/createAd", name = "createAd" )
     * @Template()
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function createAdAction()
    {
        $ad = new Ad();
        $adForm = $this->generateAdForm($ad, $this->generateUrl('createAdPost'));

        return ['form' => $adForm->createView()];
    }

    /**
     * @Route("/createAd", name = "createAdPost")
     * @Template()
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function createAdPostAction(Request $req)
    {
        $ad = new Ad();

        $form = $this->generateAdForm($ad, $this->generateUrl('createAdPost'));
        $form->handleRequest($req);

        if ($form->isValid()) {

            if ($ad->getPhotoPath() != null) {
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $ad->getPhotoPath();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $photoDir = $this->container->getParameter('kernel.root_dir') . '/../web/photos';
                $file->move($photoDir, $fileName);

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $ad->setPhotoPath($fileName);
            }
            // ... persist the $product variable or any other work

            $user = $this->getUser();

            $ad->setOwner($user);
            $user->addAd($ad);
            $ad->setViewCount(0);

            date_default_timezone_set("Europe/Warsaw");
            $date = date('Y-m-d H:i:s', time());
            $ad->setCreationDate(new \DateTime($date));

            $em = $this->getDoctrine()->getManager();

            $em->persist($ad);

            $em->flush();

            $id = $ad->getId();

            return $this->redirectToRoute('showAd', ['id' => $id]);
        } else {
            return $this->render('BoardBundle:Ad:createAd.html.twig', ['form' => $form->createView()]);
            //return $this->redirectToRoute('showAllMyActiveAds');
        }
    }

    /**
     * @Route("/modifyAd/{id}", name = "modifyAd")
     * @Template("BoardBundle:Ad:createAd.html.twig")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function modifyAdAction($id)
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);

        if ($ad->getOwner() == $user) {

            $adForm = $this->generateAdForm($ad, $this->generateUrl('modifyAdPost', ['id' => $id]));

            return ['form' => $adForm->createView()];
        } else {
            return $this->redirectToRoute('showAllAds');
        }
    }

    /**
     * @Route("/modifyAd/{id}", name = "modifyAdPost")
     * @Template("BoardBundle:Ad:createAd.html.twig")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function modifyAdPostAction(Request $req, $id)
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);

        if ($ad->getOwner() == $user) {
            $oldPath = $ad->getPhotoPath();

            $form = $this->generateAdForm($ad, $this->generateUrl('modifyAdPost', ['id' => $id]));
            $form->handleRequest($req);


            if ($form->isSubmitted() && $form->isValid()) {

                if ($ad->getPhotoPath() != null) {

                    /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                    $file = $ad->getPhotoPath();

                    if ($file != null) {
                        if ($oldPath != null && strlen($oldPath) > 0) {
                            $path = $this->container->getParameter('kernel.root_dir') . '/../web/photos/' . $oldPath;
                            unlink($path);
                        }
                    }

                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $photoDir = $this->container->getParameter('kernel.root_dir') . '/../web/photos';
                    $file->move($photoDir, $fileName);

                    // Update the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $ad->setPhotoPath($fileName);
                } else {
                    $ad->setPhotoPath($oldPath);
                }

                $user = $this->getUser();

                $ad->setOwner($user);
                $user->addAd($ad);

                $em = $this->getDoctrine()->getManager();

                $em->flush();

                $id = $ad->getId();
            }
            return $this->redirectToRoute('showAd', ['id' => $id]);
        }
        return $this->redirectToRoute('showAllAds');
    }

    /**
     * @Route("/removeAd/{id}", name = "removeAd")
     * @Security("has_role('ROLE_USER')")
     */
    public function removeAdAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);

        $user = $this->getUser();
        if ($ad->getOwner() == $user) {
            $photoPath = $ad->getPhotoPath();

            if ($photoPath != null && $photoPath > 0) {
                $path = $this->container->getParameter('kernel.root_dir') . '/../web/photos/' . $photoPath;
                unlink($path);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($ad);
            $em->flush();
        }

        return $this->redirectToRoute('showAllMyActiveAds');
    }


    /**
     * @Route("/showAd/{id}", name = "showAd")
     */
    public function showAdAction(Request $request, $id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);
        //żeby komentarze były wyświetlane od najnowszych:
        $repoComments = $this->getDoctrine()->getRepository('BoardBundle:Comment');
        $comments = $repoComments->findByCommentDate($ad);

        if ($ad->getOwner() != $this->getUser()) {

            $views = $ad->getViewCount();

//            $session = $this->getUser()->getAttribute($id);
            $session = $request->getSession()->get($id);
            if($session != 'exist') {
                $ad->setViewCount($views + 1);
                $request->getSession()->set($id, 'exist');
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
        }

        return $this->render('BoardBundle:Ad:showAd.html.twig', ['ad' => $ad, 'comments' => $comments]);
    }

    /**
     * @Route("/allAds", name = "showAllAds")
     *
     */
    public function allAdsAction(Request $request)
    {
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $em = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT a FROM BoardBundle:Ad a WHERE a.expirationDate > :nowTime ORDER BY a.creationDate DESC');
        $query->setParameter('nowTime', $dateNow);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            2/*limit per page*/
        );

        // parameters to template
        return $this->render('BoardBundle:Ad:allAds.html.twig', array('pagination' => $pagination));

    }

    /**
     * @Route("/myAds", name = "showAllMyActiveAds" )
     * @Security("has_role('ROLE_USER')")
     */
    public function myAdsActiveAction(Request $request)
    {
        date_default_timezone_set("Europe/Warsaw");
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $user = $this->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT a FROM BoardBundle:Ad a WHERE a.owner = :user AND a.expirationDate > :nowTime ORDER BY a.creationDate DESC'
        );
        $query->setParameter('user', $user);
        $query->setParameter('nowTime', $dateNow);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        // parameters to template
        return $this->render('BoardBundle:Ad:myAds.html.twig', array('pagination' => $pagination));

    }

    /**
     * @Route("/oldAds", name = "showMyOldAds" )
     * @Security("has_role('ROLE_USER')")
     */
    public function oldAdsAction(Request $request)
    {
        date_default_timezone_set("Europe/Warsaw");
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $user = $this->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT a FROM BoardBundle:Ad a WHERE a.owner = :user AND a.expirationDate < :nowTime ORDER BY a.creationDate DESC'
        );
        $query->setParameter('user', $user);
        $query->setParameter('nowTime', $dateNow);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        // parameters to template
        return $this->render('BoardBundle:Ad:oldAds.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/search", name = "searchPost" )
     * @Method("POST")
     */
    public function searchPostAction(Request $request)
    {
        $search = $request->request->get('name');
        $em = $this->get('doctrine.orm.entity_manager');
        date_default_timezone_set("Europe/Warsaw");
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));

        $query = $em->createQuery(
            'SELECT a FROM BoardBundle:Ad a WHERE a.expirationDate > :nowTime AND a.title LIKE :search ORDER BY a.creationDate DESC'
        );
        $query->setParameter('search', '%' . $search . '%');
        $query->setParameter('nowTime', $dateNow);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        // parameters to template
        return $this->render('BoardBundle:Ad:allAds.html.twig', array('pagination' => $pagination, 'no' => 'no'));

    }


}
