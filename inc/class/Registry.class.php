<?php
/**
 * Arquivo que traz a classe de Registros
 * 
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     Registry
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Registry do CMS
 * 
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Registry
{
    /**
     * @var Registry
     * @access private
     */
    private static $_instance;
    /**
     * @var ArrayObject
     * @access private
     */
    private $_storage;

    /**
     * Função que constrói a classe
     *
     * @return void
     * @access private
     */
    private function __construct()
    {
        $this->_storage = new ArrayObject();
    }


    /**
     * Função que impede a clonagem da classe
     * pelo método mágico
     *
     * @return void
     * @access private
     */
    private function __clone()
    {
    }


    /**
     * Recupera a instância única de Registry
     * 
     * @return Registry
     * @access public
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new Registry();
        }
        return self::$_instance;
    }


    /**
     * Função que adiciona elementos no registry
     * 
     * @param String $value {Objeto ou valor a ser armazenado}
     * @param String $key   {Nome a ser armazenado}
     * 
     * @return void
     * @access public
     */
    public function set($key , $value) {
        if (!$this->_storage->offsetExists($key)) {
            $this->_storage->offsetSet($key , $value);
        } else {
            throw new LogicException(sprintf('Já existe um registro para a chave "%s".' , $key));
        }
    }


    /**
     * Função que busca elementos no registry
     * 
     * @param String $key {Nome a ser buscado}
     * 
     * @return Object
     * @access public
     */
    public function get($key) {
        if ($this->_storage->offsetExists($key)) {
            return $this->_storage->offsetGet($key);
        } else {
            throw new RuntimeException(sprintf('Não existe um registro para a chave "%s".' , $key));
        }
    }


    /**
     * Função que verifica se existe valores com determinado nome
     * 
     * @param String $key {Nome a ser buscado}
     * 
     * @return Boolean
     * @access public
     */
    public function exists($key)
    {
        if ($this->_storage->offsetExists($key)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui valores com determinado nome
     * 
     * @param String $key {Nome a ser buscado}
     * 
     * @return void
     * @access public
     */
    public function unregister($key) {
        if ($this->_storage->offsetExists($key)) {
            $this->_storage->offsetUnset($key);
        } else {
            throw new RuntimeException(sprintf('Não existe um registro para a chave "%s".' , $key));
        }
    }


}
?>