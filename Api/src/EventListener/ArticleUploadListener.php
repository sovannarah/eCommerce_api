<?php


namespace App\EventListener;


use App\Entity\Article;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleUploadListener
{

    private $images_directory;

    public function __construct(string $images_directory)
    {
        $this->images_directory = $images_directory;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->_uploadImages($args->getEntity());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->_uploadImages($args->getEntity());
    }

    private function _uploadImages($entity): void
    {
        if (!$entity instanceof Article) {
            return;
        }
        $imageNames = [];
        foreach ($entity->getImages() as $image) {
            if ($image instanceof UploadedFile) {
                $fileName = md5(uniqid($entity->getId(), true))
                    .'.'.$image->guessExtension();
                $image->move($this->images_directory, $fileName);
                $imageNames[] = $fileName;
            } else {
                $imageNames[] = $image->getBasename();
            }
        }
        $entity->setImages($imageNames);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Article) {
            return;
        }
        $imageFiles = [];
        foreach ($entity->getImages() as $path) {
            if (!is_string($path)) {
                continue;
            }
            $imageFiles[] = new File(
                $this->images_directory.DIRECTORY_SEPARATOR.$path
            );
        }
        $entity->setImages($imageFiles);
    }
}
