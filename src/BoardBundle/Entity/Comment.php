<?php

namespace BoardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BoardBundle\Entity\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="commentText", type="string", length=255)
     */
    private $commentText;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="commentDate", type="datetime")
     */
    private $commentDate;


    /**
     * @ORM\ManyToOne( targetEntity = "Ad", inversedBy = "adComments" )
     * @ORM\JoinColumn( name = "ad_id", referencedColumnName = "id", onDelete = "CASCADE" )
     */
    private $itsAd;


    /**
     * @ORM\ManyToOne( targetEntity = "User", inversedBy = "userComments" )
     * @ORM\JoinColumn( name = "user_id", referencedColumnName = "id", onDelete = "CASCADE" )
     */
    private $commentOwner;


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
     * Set commentText
     *
     * @param string $commentText
     * @return Comment
     */
    public function setCommentText($commentText)
    {
        $this->commentText = $commentText;

        return $this;
    }

    /**
     * Get commentText
     *
     * @return string 
     */
    public function getCommentText()
    {
        return $this->commentText;
    }

    /**
     * Set commentDate
     *
     * @param \DateTime $commentDate
     * @return Comment
     */
    public function setCommentDate($commentDate)
    {
        $this->commentDate = $commentDate;

        return $this;
    }

    /**
     * Get commentDate
     *
     * @return \DateTime 
     */
    public function getCommentDate()
    {
        return $this->commentDate;
    }

    /**
     * Set itsAd
     *
     * @param \BoardBundle\Entity\Ad $itsAd
     * @return Comment
     */
    public function setItsAd(\BoardBundle\Entity\Ad $itsAd = null)
    {
        $this->itsAd = $itsAd;

        return $this;
    }

    /**
     * Get itsAd
     *
     * @return \BoardBundle\Entity\Ad 
     */
    public function getItsAd()
    {
        return $this->itsAd;
    }

    /**
     * Set commentOwner
     *
     * @param \BoardBundle\Entity\User $commentOwner
     * @return Comment
     */
    public function setCommentOwner(\BoardBundle\Entity\User $commentOwner = null)
    {
        $this->commentOwner = $commentOwner;

        return $this;
    }

    /**
     * Get commentOwner
     *
     * @return \BoardBundle\Entity\User 
     */
    public function getCommentOwner()
    {
        return $this->commentOwner;
    }
}
