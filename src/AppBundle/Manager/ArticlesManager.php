<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 09/03/2017
 * Time: 09:01
 */

namespace AppBundle\Manager;


use AppBundle\Entity\Article;
use Doctrine\ORM\EntityManager;

class ArticlesManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function persistArticle(Article $article)
    {
        $this->em->persist($article);
        $this->em->flush();
    }

    public function removeArticle(Article $article)
    {
        $this->em->remove($article);
        $this->em->flush();
    }
}
