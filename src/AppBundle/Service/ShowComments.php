<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 09/03/2017
 * Time: 14:13
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commentaires;
use Doctrine\ORM\EntityManager;

class ShowComments
{

    private $em;
    private $maxResults = 5;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handlePagination($offset, $commentsType = null)
    {
        $paginationData = [];
        $paginationData['nbPages'] = ($commentsType == 'flagged') ? $this->countPagesForFlaggedComments() : $this->countPages();
        $paginationData['currentPage'] = $offset;
        return $paginationData;
    }

    public function getComments($offset)
    {
        $offset = $offset * $this->maxResults;
        return $this->em->getRepository('AppBundle:Commentaires')->showComments($offset, $this->maxResults);
    }

    public function getFlaggedComments($offset)
    {
        $offset = $offset * $this->maxResults;
        return $this->em->getRepository('AppBundle:Commentaires')->showFlaggedComments($offset, $this->maxResults);
    }

    public function countFlaggedComments()
    {
        return count($this->em->getRepository('AppBundle:Commentaires')->getFlaggedComments());
    }

    public function countUnreadComments()
    {
        return count($this->em->getRepository('AppBundle:Commentaires')->getUnreadComments());
    }

    public function countPages()
    {
        return ceil(count($this->em->getRepository('AppBundle:Commentaires')->findAll())/$this->maxResults);
    }

    public function countPagesForFlaggedComments()
    {
        return ceil(count($this->em->getRepository('AppBundle:Commentaires')->getFlaggedComments())/$this->maxResults);
    }

    public function markCommentsAsRead()
    {
        $commentaires = $this->em->getRepository('AppBundle:Commentaires')->getUnreadComments();
        foreach($commentaires as $commentaire)
        {
            /**
             * @var Commentaires $commentaire
             */
            $commentaire->setStatus('READ');
            $this->em->persist($commentaire);
        }
        $this->em->flush();
    }
}