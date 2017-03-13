<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 03/03/2017
 * Time: 11:14
 */

namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\File\File;

class FileUploader
{

    private $directoryPath;

    public function __construct($directoryPath)
    {
        $this->directoryPath = $directoryPath;
    }

    public function uploadFile(File $file)
    {
        $filename = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->directoryPath, $filename);
        return $filename;
    }
}
