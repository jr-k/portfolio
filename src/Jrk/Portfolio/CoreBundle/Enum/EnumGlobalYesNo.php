<?php

namespace Jrk\Portfolio\CoreBundle\Enum;

class EnumGlobalYesNo
{

    const YES = 1;
    const NO = 0;

    public static $TOSTRING = array(
        self::YES => "Oui",
        self::NO => "Non",
    );


}