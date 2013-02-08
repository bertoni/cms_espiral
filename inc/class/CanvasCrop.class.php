<?php

define("ccTOPLEFT",     0);
define("ccTOP",         1);
define("ccTOPRIGHT",    2);
define("ccLEFT",        3);
define("ccCENTRE",      4);
define("ccCENTER",      4);
define("ccRIGHT",       5);
define("ccBOTTOMLEFT",  6);
define("ccBOTTOM",      7);
define("ccBOTTOMRIGHT", 8);

class canvasCrop extends CropCanvas
{
    /**
     * Class constructor.
     * 
     * @param  string $debug 
     * @return cavasCrop 
     * @access public
     */
    function cavasCrop($debug = false)
    {
        parent::CropCanvas($debug);
    }

}
?>