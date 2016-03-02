<?php

namespace BoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommentController extends Controller
{
    /**
     * @Route("/myComments")
     * @Template()
     */
    public function myCommentsAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/showComment")
     * @Template()
     */
    public function showCommentAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/addComment")
     * @Template()
     */
    public function addCommentAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/removeComment")
     * @Template()
     */
    public function removeCommentAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/allComments")
     * @Template()
     */
    public function allCommentsAction()
    {
        return array(
                // ...
            );    }

}
