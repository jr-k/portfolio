<?php

namespace Jrk\Portfolio\CoreBundle\UploaderAdapters;



trait Uploadable
{


    public function getUploadDirectoryPath($create = false)
    {
        $dir = 'uploads/'.basename(str_replace('\\','/',strtolower(__CLASS__))).'-'.substr(md5(get_class($this)),0,6).'/'.implode('/',str_split($this->getId()));

        if (!is_dir($dir) && $create) {
            mkdir($dir,0777,true);
        }

        return $dir;
    }

    public function getResourceDirectoryPath()
    {
        return 'resources/'.basename(str_replace('\\','/',strtolower(__CLASS__))).'-'.substr(md5(get_class($this)),0,6);
    }

    public function getPicture($defaultResource = true)
    {
        $file = $this->getFile();

        if ($file != null) {
            return $file;
        }

        if (!$defaultResource) {
            return null;
        }

        return $this->getResourceDirectoryPath().'/picture.jpg';
    }

    public function getFile()
    {
        $dir = $this->getUploadDirectoryPath();
        $files = array();

        if(is_dir($dir)){
            $files = scandir($dir);

            foreach($files as $imgNameKey => $imgName){
                if($imgName !== '.' && $imgName !== '..'){
                    if (!is_dir($dir.'/'.$imgName)) {
                        return $dir.'/'.$imgName;
                    }
                }
            }
        }

        return null;
    }
}