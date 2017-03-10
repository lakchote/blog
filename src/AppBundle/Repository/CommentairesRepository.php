<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 07/03/2017
 * Time: 12:17
 */

namespace AppBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CommentairesRepository extends NestedTreeRepository
{
    public function getFlaggedComments()
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.isFlagged = :boolean')
            ->orderBy('c.countIsFlagged', 'DESC')
            ->setParameter('boolean', true)
            ->getQuery()
            ->getResult();
    }

    public function getUnreadComments()
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'NOT_READ')
            ->getQuery()
            ->getResult();
    }

    public function showComments($offset, $maxResults)
    {
        return $this
            ->createQueryBuilder('c')
            ->setFirstResult($offset)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    public function showFlaggedComments($offset, $maxResults)
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.isFlagged = :boolean')
            ->orderBy('c.countIsFlagged', 'DESC')
            ->setParameter('boolean', true)
            ->setFirstResult($offset)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }
}