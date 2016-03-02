<?php

// src/BoardBundle/Entity/User.php

namespace BoardBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="fos_user")
*/
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany( targetEntity = "Ad", mappedBy ="owner")
     */
    protected $ads;

    /**
     * Add ads
     *
     * @param \BoardBundle\Entity\Ad $ads
     * @return User
     */
    public function addAd(\BoardBundle\Entity\Ad $ads)
    {
        $this->ads[] = $ads;

        return $this;
    }

    /**
     * Remove ads
     *
     * @param \BoardBundle\Entity\Ad $ads
     */
    public function removeAd(\BoardBundle\Entity\Ad $ads)
    {
        $this->ads->removeElement($ads);
    }

    /**
     * Get ads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAds()
    {
        return $this->ads;
    }
}
