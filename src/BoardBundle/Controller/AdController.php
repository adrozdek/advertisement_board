<?php

namespace BoardBundle\Controller;

use BoardBundle\Entity\Ad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class AdController extends Controller
{
    private function generateAdForm($ad, $action)
    {
        $form = $this->createFormBuilder($ad);
        $form->add('title', 'text', ['trim' => true]);
        $form->add('description', 'textarea', ['trim' => true]);
        $form->add('photoPath', 'file', ['data_class' => null, 'required' => false]);
        $form->add('expirationDate', 'datetime');
        $form->add('categories', 'entity', [
            'class' => 'BoardBundle\Entity\Category',
            'property' => 'name',
            'required' => false,
            'multiple' => true,
            'expanded' => true]);
        $form->add('save', 'submit');
        $form->setAction($action);

        $adFrom = $form->getForm();

        return $adFrom;
    }

    /**
     * @Route("/createAd", name = "createAd" )
     * @Template()
     * @Method("GET")
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
     */
    public function createAdPostAction(Request $req)
    {
        $ad = new Ad();

        $form = $this->generateAdForm($ad, $this->generateUrl('createAdPost'));
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

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

            //@TODO: wrong time. -1h

            $date = date('Y-m-d H:i:s', time());
            $ad->setCreationDate(new \DateTime($date));

            $em = $this->getDoctrine()->getManager();

            $em->persist($ad);

            $em->flush();

            $id = $ad->getId();

            return $this->redirectToRoute('showAd', ['id' => $id]);
        } else {
            return $this->redirectToRoute('showAllMyAds');
        }
    }

    /**
     * @Route("/modifyAd/{id}", name = "modifyAd")
     * @Template("BoardBundle:Ad:createAd.html.twig")
     * @Method("GET")
     */
    public function modifyAdAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);

        $adForm = $this->generateAdForm($ad, $this->generateUrl('modifyAdPost', ['id' => $id]));

        return ['form' => $adForm->createView()];
    }

    /**
     * @Route("/modifyAd/{id}", name = "modifyAdPost")
     * @Template("BoardBundle:Ad:createAd.html.twig")
     * @Method("POST")
     */
    public function modifyAdPostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);

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

    /**
     * @Route("/removeAd/{id}", name = "removeAd")
     */
    public function removeAdAction($id) {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);

        $photoPath = $ad->getPhotoPath();

        if ($photoPath != null && $photoPath > 0) {
            $path = $this->container->getParameter('kernel.root_dir') . '/../web/photos/' . $photoPath;
            unlink($path);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($ad);
        $em->flush();

        return $this->redirectToRoute('showAllMyActiveAds');
    }


    /**
     * @Route("/showAd/{id}", name = "showAd")
     * @Template()
     */
    public function showAdAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ad = $repo->find($id);
        //żeby komentarze były wyświetlane od najnowszych:
        $repoComments = $this->getDoctrine()->getRepository('BoardBundle:Comment');
        $comments = $repoComments->findByCommentDate($ad);

        return ['ad' => $ad, 'comments' => $comments];
    }

    /**
     * @Route("/allAds", name = "showAllAds")
     * @Template()
     */
    public function allAdsAction()
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
        $ads = $repo->findAll();

        return ['ads' => $ads];
    }

    /**
     * @Route("/myAds", name = "showAllMyActiveAds" )
     * @Template("BoardBundle:Ad:myAds.html.twig")
     */
    public function myAdsActiveAction()
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');

        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));

        $myAds = $repo->findByUserActive($this->getUser(), $dateNow);
        //orderedBy creationDate DESC
        //@TODO: albo opcja order by expirationDate

        return ['ads' => $myAds ];
    }

    /**
     * @Route("/oldAds", name = "showMyOldAds" )
     * @Template()
     */
    public function oldAdsAction()
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');

        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));

        $myAds = $repo->findByUserOld($this->getUser(), $dateNow);
        //orderedBy creationDate DESC
        //@TODO: albo opcja order by expirationDate

        return ['ads' => $myAds ];
    }



}
