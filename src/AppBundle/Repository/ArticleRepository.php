<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 06/03/2017
 * Time: 15:54
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function showArticles($offset, $maxResults)
    {
        return $this
            ->createQueryBuilder('a')
            ->setFirstResult($offset)
            ->setMaxResults($maxResults)
            ->orderBy('a.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSearchResults($userInput)
    {
        return $this
            ->createQueryBuilder('a')
            ->where('a.titre LIKE :userInput')
            ->setParameter('userInput', '%' . $userInput . '%')
            ->getQuery()
            ->getResult();
    }
}
