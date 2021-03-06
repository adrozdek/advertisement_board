<?php

namespace BoardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ad
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BoardBundle\Entity\AdRepository")
 */
class Ad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=130)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="photoPath", type="string", length=255)
     * @Assert\File()
     */
    private $photoPath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expirationDate", type="datetime")
     */
    private $expirationDate;

    /**
     * @ORM\Column( name = "viewCount",  type = "integer")
     */
    private $viewCount;


    /**
     * @ORM\ManyToOne( targetEntity = "User", inversedBy = "ads" )
     * @ORM\JoinColumn( name = "owner_id", referencedColumnName = "id", onDelete="CASCADE")
     */
    private $owner;

    /**
     * @ORM\ManyToMany( targetEntity = "Category", inversedBy = "ads" )
     * @ORM\JoinTable( name = "ad_category" )
     */
    private $categories;

    /**
     * @ORM\OneToMany( targetEntity = "Comment", mappedBy = "itsAd" )
     */
    protected $adComments;

    /**
     * @ORM\Column(name = "creationDate", type="datetime")
     */
    protected $creationDate;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Ad
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Ad
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set photoPath
     *
     * @param string $photoPath
     * @return Ad
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    /**
     * Get photoPath
     *
     * @return string 
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     * @return Ad
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime 
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set owner
     *
     * @param \BoardBundle\Entity\User $owner
     * @return Ad
     */
    public function setOwner(\BoardBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \BoardBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add categories
     *
     * @param \BoardBundle\Entity\Category $categories
     * @return Ad
     */
    public function addCategory(\BoardBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \BoardBundle\Entity\Category $categories
     */
    public function removeCategory(\BoardBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }


    /**
     * Add adComments
     *
     * @param \BoardBundle\Entity\Comment $adComments
     * @return Ad
     */
    public function addAdComment(\BoardBundle\Entity\Comment $adComments)
    {
        $this->adComments[] = $adComments;

        return $this;
    }

    /**
     * Remove adComments
     *
     * @param \BoardBundle\Entity\Comment $adComments
     */
    public function removeAdComment(\BoardBundle\Entity\Comment $adComments)
    {
        $this->adComments->removeElement($adComments);
    }

    /**
     * Get adComments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdComments()
    {
        return $this->adComments;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Ad
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set viewCount
     *
     * @param integer $viewCount
     * @return Ad
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Get viewCount
     *
     * @return integer 
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }
}
