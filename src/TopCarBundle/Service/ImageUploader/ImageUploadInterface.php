<?php


namespace TopCarBundle\Service\ImageUploader;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageUploadInterface
{
    public function upload(UploadedFile $image);

    public function getTargetDirectory();

}