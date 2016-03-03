<?php

namespace BoardBundle\Controller;

use BoardBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    private function generateCommentFrom($comment, $action)
    {
        $form = $this->createFormBuilder($comment);
        $form->add('commentText', 'textarea', ['trim' => true]);
        $form->add('save', 'submit');
        $form->setAction($action);

        $commentFrom = $form->getForm();

        return $commentFrom;
    }

    /**
     * @Route("/addComment/{id}", name = "addComment")
     * @Template()
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function addCommentAction($id)
    {
        $comment = new Comment();
        $commentForm = $this->generateCommentFrom($comment, $this->generateUrl('addCommentPost', ['id' => $id]));

        return ['form' => $commentForm->createView()];
    }

    /**
     * @Route("/addComment/{id}", name = "addCommentPost")
     * @Template()
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function addCommentPostAction(Request $req, $id)
    {
        $comment = new Comment();

        $form = $this->generateCommentFrom($comment, $this->generateUrl('addCommentPost', ['id' => $id]));
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $repo = $this->getDoctrine()->getRepository('BoardBundle:Ad');
            $ad = $repo->find($id);

            $user = $this->getUser();

            date_default_timezone_set("Europe/Warsaw");
            $date = date('Y-m-d H:i:s', time());
            $comment->setCommentDate(new \DateTime($date));

            $comment->setCommentOwner($user);
            $user->addUserComment($comment);

            $comment->setItsAd($ad);
            $ad->addAdComment($comment);

            $em = $this->getDoctrine()->getManager();

            $em->persist($comment);

            $em->flush();
        }

        return $this->redirectToRoute('showAd', ['id' => $id]);

    }

    //@TODO: ewentualna edycja komentarzy

    /**
     * @Route("/myComments", name = "showMyComments")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     *
     */
    public function myCommentsAction()
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Comment');
        $comments = $repo->findCommentsByUser($this->getUser());

        return ['comments' => $comments];
    }


    /**
     * @Route("/removeComment/{id}", name = "removeComment" )
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function removeCommentAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('BoardBundle:Comment');
        $comment = $repo->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        //@TODO: redirect to previous watching page

        return $this->redirectToRoute('showAllMyActiveAds');

    }

}
