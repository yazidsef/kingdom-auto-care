<?php

namespace App\Service;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Filesystem\Filesystem;

class ImageManipulator{
    private $imagine; 
    private $uploaderHelper;
    private $filesystem;

    public function __construct(ImagineInterface $imagine , UploaderHelper $uploaderHelper){
        $this->imagine = $imagine;
        $this->uploaderHelper = $uploaderHelper;
        $this->filesystem = new Filesystem();
    }

    public function resize($entity , string $property , int $width , int $height , string $targetDir):void 
    {
        $path = $this->uploaderHelper->asset($entity , $property);
        $image = $this->imagine->open($path);
        $resizedImage = $image->thumbnail(new Box ($width , $height), ImageInterface::THUMBNAIL_OUTBOUND);   
    
        //ensure target directory exists
        $this->filesystem->mkdir($targetDir);

        $imageName = pathinfo(basename($path), PATHINFO_FILENAME) . '.webp';
        $resizedImage->save($targetDir . '/' . $imageName , ['webp_quality' => 75]);

    }
}