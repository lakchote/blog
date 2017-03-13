<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 12/03/2017
 * Time: 18:38
 */

namespace Twig;


class OutputHtml extends \Twig_Extension
{
    public function getFilters()
    {
        return [
          new \Twig_SimpleFilter('outputHtml', [$this, 'outputHtml'], ['is_safe' => ['html'] ])
        ];
    }

    public function outputHtml($html)
    {
        return $html;
    }

    public function getName()
    {
        return 'output_html';
    }
}
