<?php 

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture , ?string $folder , ?int $width=250 , ?int $height = 250)
    {
        //on donne un nouveau nom a l'image avec la fonction uniqid qui genere un id unique 
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        //on recupere les infos de l'image largeur et hateur ...
        $picture_infos = getimagesize($picture);

        if($picture_infos === false)
        {
            throw new \Exception('Impossible de lire l\'image');
        }

        //on verifie le format d'image
        switch ($picture_infos['mime']){
            case 'image/png':
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break; 
            case 'image/webp':
                $picture_source = imagecreatefromwebp($picture);
                break;
            default:
            throw new \Exception('Impossible de lire l\'image');

        }

        //on recadre l'image
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        //on verifie l'orientation de l'image

        switch ($imageWidth <=> $imageHeight)
        {
            case -1: //portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize / 2);
                break;
            case 0: //carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1: //paysage
                $squareSize = $imageHeight;
                $src_y = 0;
                $src_x = ($imageWidth - $squareSize / 2);
                break;
        }

        //on cree une nouvelle image vierge

        $resized_Picture = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized_Picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);
        $path =$this->params->get('images_directory').$folder;

        //on fait le dossier s'il n'existe pas
        if(!file_exists($path.'/mini/'))
        {
            mkdir($path.'/mini/', 0755, true);
        }
        
        //on stock l'image recadrée 
        imagewebp($resized_Picture, $path.'/mini/'.$width . 'x' .$height . '-' . $fichier);

        $picture ->move($path . '/' , $fichier);
        return $fichier;
    }

    public function delete(string $fichier , ?string $folder = '' , ?int $width=250 , ?int $height = 250 )
    {
        if($fichier !== 'default.webp')
        {
            $success = false ;
            $path = $this->params->get('images_directory') .$folder;
            $mini =  $path.'/mini/'.$width . 'x' .$height . '-' . $fichier;

            if(file_exists($mini))
            {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' .$fichier ;
            if(file_exists($original))
            {
                unlink($mini);
                $success = true;
            }
            return $success ;
        }
        return false ; 
    }
}