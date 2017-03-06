<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 06/03/2017
 * Time: 21:55
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;

class ShowArticles
{

    private $em;
    private $maxResults = 5;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handlePagination($offset)
    {
        $paginationData = [];
        $paginationData['nbPages'] = $this->countPages();
        $paginationData['currentPage'] = $offset;
        return $paginationData;
    }

    public function getArticles($offset)
    {
        $offset = $offset * $this->maxResults;
        return $this->em->getRepository('AppBundle:Article')->showArticles($offset, $this->maxResults);
    }

    public function countPages()
    {
        return ceil(count($this->em->getRepository('AppBundle:Article')->findAll())/$this->maxResults);
    }
}