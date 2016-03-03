<?php

namespace BoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FirstController extends Controller
{
    //@TODO: profil użytkownika z danymi. do edycji
    //@TODO: linki do wiadomości. itp.

    //@TODO: opcje wysyłania wiadomości do uzytkownika.?

    /**
     * @Route("/profile", name = "showProfile" )
     * @Template("BoardBundle:User:userProfile.html.twig")
     * @Security("has_role('ROLE_USER')")
     */
    public function showProfileAction() {
        $user = $this->getUser();

        return ['user' => $user];

    }



}
