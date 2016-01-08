<?php

namespace Jrk\Portfolio\CoreBundle\Enum;

class EnumConfigurationVartype {

    const TEXT = 0;
    const TEXTAREA = 1;
    const RICHTEXT = 2;
    const FILEUPLOAD = 3;


    public static $TOSTRING = array(
        self::TEXT => 'Champ de texte',
        self::TEXTAREA => 'Zone de texte',
        self::RICHTEXT => 'Zone de texte riche',
        self::FILEUPLOAD => 'Upload de fichier',
    );

    public static $FILE_ASSOCIATION = array(
        self::TEXT => 'text',
        self::TEXTAREA => 'textarea',
        self::RICHTEXT => 'ckeditor',
        self::FILEUPLOAD => 'file',
    );

}