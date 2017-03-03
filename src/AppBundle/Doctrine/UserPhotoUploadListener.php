<?php
/**
 * Created by PhpStorm.
 * User: BRANDON HEAT
 * Date: 03/03/2017
 * Time: 11:20
 */

namespace AppBundle\Doctrine;


use AppBundle\Entity\User;
use AppBundle\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;

class UserPhotoUploadListener implements EventSubscriber
{

    private $fileUploader;
    private $directoryPath;

    public function __construct(FileUploader $fileUploader, $directoryPath)
    {
        $this->fileUploader = $fileUploader;
        $this->directoryPath = $directoryPath;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $filename = $entity->getPhoto();
        if($filename != '') $entity->setPhoto(new File($this->directoryPath . '/' . $filename));
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate', 'postLoad'];
    }

    public function uploadFile($entity)
    {
        if(!$entity instanceof User)
        {
            return;
        }

        $file = $entity->getPhoto();

        if(!$file instanceof File)
        {
            return;
        }

        $filename = $this->fileUploader->uploadFile($file);
        $entity->setPhoto($filename);
    }
}