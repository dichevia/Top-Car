<?php


namespace TopCarBundle\Service\ImageUploader;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AvatarUploader implements ImageUploadInterface
{

    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $image)
    {
        $safeFileName = md5(uniqid());

        $imageName = 'avatar_'.$safeFileName.'.'.$image->guessExtension();

        try {
            $image->move($this->getTargetDirectory(), $imageName);
        } catch (FileException $e) {

        }

        return $imageName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}