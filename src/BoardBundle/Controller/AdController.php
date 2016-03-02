<?php

namespace BoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdController extends Controller
{
    /**
     * @Route("/createAd")
     * @Template()
     */
    public function createAdAction()
    {
        return [];
    }

    /**
     * @Route("/allAds")
     * @Template()
     */
    public function allAdsAction()
    {
        return [];
    }

    /**
     * @Route("/myAds")
     * @Template()
     */
    public function myAdsAction()
    {
        return [];
    }

    /**
     * @Route("/oldAds")
     * @Template()
     */
    public function oldAdsAction()
    {
        return [];
    }

}
