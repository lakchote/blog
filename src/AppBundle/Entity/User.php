<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    private $imgPath = '/uploads/user';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Vous devez indiquer votre nom.")
     * @Assert\Length(max="50", maxMessage="Le nom ne peut excéder 50 caractères.")
     */
    private $nom;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Vous devez indiquer votre prénom.")
     * @Assert\Length(max="50", maxMessage="Le prénom ne peut excéder 50 caractères.")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string")
     * @Assert\Email(message="L'adresse email n'est pas valide", checkMX=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"Registration"}, message="Vous devez indiquer votre mot de passe.")
     * @Assert\Length(groups={"Registration"}, min=6, max=12, minMessage="Le mot de passe doit faire 6 caractères au minimum.", maxMessage="Le mot de passe doit faire 12 caractères au maximum.")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $resetPassword;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(mimeTypes={"image/jpeg", "image/png"})
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="user")
     */
    private $commentaire;

    public function __construct()
    {
        $this->commentaire = new ArrayCollection();
    }

    public function getRoles()
    {
        $roles = $this->roles;
        if(!in_array('ROLE_USER', $roles)) {
            $roles = 'ROLE_USER';
        }
        return $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->prenom . $this->nom;
    }

    public function eraseCredentials()
    {
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function getResetPassword()
    {
        return $this->resetPassword;
    }

    public function setResetPassword($resetPassword)
    {
        $this->resetPassword = $resetPassword;
    }

    public function getPhoto()
    {
        return $this->photo;
    }


    public function setPhoto($photo)
    {
        $this->photo = $photo;
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
        $commentaire->setUser($this);
    }

    public function getImgPath()
    {
        return $this->imgPath;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRoles($roles)
    {
        $this->roles[] = $roles;
    }
}