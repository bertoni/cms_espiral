<?php
/**
 * Arquivo que traz a classe de Internacionalização
 * 
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     I18n
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Internacionalização
 * 
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class I18n
{
    private $_content = null;
    
    /**
     * Função que constrói o objeto
     * 
     * @param String $type_lang {Linguagem a ser usada}
     *
     * @return void
     * @access public
     */
    public function __construct($type_lang)
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/inc/i18n/' . $type_lang . '.php')) {
            require_once  $_SERVER['DOCUMENT_ROOT'] . '/inc/i18n/' . $type_lang . '.php';
        } else {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/i18n/pt_br.php';
        }
        $this->_content = $TEXT;
    }
    

    /**
     * Função que busca uma string no idioma utilizado
     * 
     * @param String $str {String a ser traduzida}
     * 
     * @return String
     * @access public
     */
    public function _($str)
    {
        if (array_key_exists($str, $this->_content)) {
            return $this->_content[$str];
        } else {
            return $str;
        }
    }


}
?>