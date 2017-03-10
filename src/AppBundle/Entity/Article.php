<?php

namespace  AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
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
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max="100", maxMessage="Le titre de l'article est trop grand")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $contenu;

    /**
     * @ORM\Column(type="date")
     */
    private $datePublication;

    /**
     * @ORM\OneToMany(targetEntity="Commentaires", mappedBy="article", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $commentaires;

    /**
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(type="string")
     */
    private $slug;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->datePublication = new \DateTime();
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

    public function getId()
    {
        return $this->id;
    }

    public function addCommentaire($commentaire)
    {
        $this->commentaires[] = $commentaire;

        return $this;
    }

    public function removeCommentaire($commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Get commentaires
     *
     * @return ArrayCollection
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getExcerpt()
    {
        $contenu = strip_tags($this->getContenu());
        $excerpt = substr($contenu, 0, 300) . '[...]';
        return $excerpt;
    }
}
