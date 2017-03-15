<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentairesRepository")
 * @ORM\Table(name="commentaires")
 */
class Commentaires
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="commentaires", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="commentaires", cascade={"persist"})
     */
    private $article;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min="15", max="1000")
     */
    private $contenu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFlagged = false;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countIsFlagged = 0;


    /**
     * @ORM\Column(type="string")
     */
    private $status = 'NOT_READ';

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Commentaires")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Commentaires", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Commentaires", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    public function __construct()
    {
        $this->dateOfPost = new \DateTime();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }


    public function getArticle()
    {
        return $this->article;
    }


    public function setArticle($article)
    {
        $this->article = $article;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    public function getFlagged()
    {
        return $this->isFlagged;
    }

    public function setFlagged($isFlagged)
    {
        $this->isFlagged = $isFlagged;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Commentaires $parent = null)
    {
        $this->parent = $parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getLvl()
    {
        return $this->lvl;
    }

    public function getCountIsFlagged()
    {
        return $this->countIsFlagged;
    }

    public function setCountIsFlagged()
    {
        $this->countIsFlagged += 1;
    }

    public function resetCountIsFlagged()
    {
        $this->countIsFlagged = 0;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
