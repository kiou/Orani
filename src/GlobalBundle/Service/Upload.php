<?php

	namespace GlobalBundle\Service;

    use Cocur\Slugify\Slugify;

	class Upload{

        /**
         * @param $name le nom du fichier
         * @param $file le nom du dossier ou ce trouve le fichier
         */
	    public function createName($name, $file){

            $slugify = new Slugify();

            $nameInfo = pathinfo($file.strtolower($name), PATHINFO_FILENAME);
            $nameExt =  pathinfo($file.strtolower($name), PATHINFO_EXTENSION);
            $nameSlug = $slugify->slugify($nameInfo);
            $nameSlugExt = $nameSlug.'.'.$nameExt;

	        if (file_exists($file.$nameSlugExt)){
                $name = $nameSlug.'_'.uniqid().'.'.$nameExt;
                return $name;
            }

            return $nameSlugExt;

        }

	}

?>