<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile
{
    private $targetDoc;

    public function __construct($targetDoc)
    {
        $this->targetDoc = $targetDoc;
    }

    public function uploadFile(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetFile(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $fileName;
    }

    public function getTargetFile()
    {
        return $this->targetDoc;
    }
}
