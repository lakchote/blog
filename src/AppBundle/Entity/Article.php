<?php

namespace  AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(type="date")
     */
    private $datePublication;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="article", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $commentaire;

    public function __construct()
    {
        $this->commentaire = new ArrayCollection();
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    public function getDatePublication()
    {
        return $this->datePublication;
    }


    public function setDatePublication($datePublication)
    {
        $this->datePublication = $datePublication;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }


    public function setCommentaire(Commentaire $commentaire)
    {
        $this->commentaire->add($commentaire);
        $commentaire->setArticle($this);
    }

    public function getId()
    {
        return $this->id;
    }
}