<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 07/03/2017
 * Time: 10:48
 */

namespace AppBundle\Manager;

use AppBundle\Entity\Article;
use AppBundle\Entity\Commentaires;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentsManager
{
    private $em;
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function persistNewComment(Commentaires $formData, Article $article)
    {
        $formData->setUser($this->tokenStorage->getToken()->getUser());
        $formData->setArticle($article);
        $this->em->persist($formData);
        $this->em->flush();
    }

    public function answerComment(Commentaires $commentaire, Commentaires $formData, Article $article)
    {
        $formData->setUser($this->tokenStorage->getToken()->getUser());
        $formData->setParent($commentaire);
        $formData->setArticle($article);
        $this->em->persist($formData);
        $this->em->flush();
    }

    public function reportComment(Commentaires $commentaire)
    {
        $commentaire->setIsFlagged(true);
        $commentaire->setCountIsFlagged();
        $this->em->persist($commentaire);
        $this->em->flush();
    }

    public function deleteComment(Commentaires $commentaire)
    {
        $this->em->remove($commentaire);
        $this->em->flush();
    }

    public function resetCommentFlaggedStatus(Commentaires $commentaire)
    {
        $commentaire->setIsFlagged(false);
        $commentaire->resetCountIsFlagged();
        $this->em->persist($commentaire);
        $this->em->flush();
    }
}