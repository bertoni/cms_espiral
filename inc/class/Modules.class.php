<?php
/**
 * Arquivo que traz a classe de Módulos do CMS
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     Modules
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Módulos do CMS
 *
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Modules
{
    /**
     * @var String
     * @access protected
     */
    protected $id_module;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var String
     * @access protected
     */
    protected $name_module;
    /**
     * @var String
     * @access protected
     */
    protected $url;
    /**
     * @var String
     * @access protected
     */
    protected $log;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_modules';

    /**
     * Função que faz buscar os atributos
     *
     * @param String $attribute {Atributo a ser buscado}
     *
     * @return String
     * @access public
     */
    public function getAttribute($attribute)
    {
        switch ($attribute) {
        case   'id_module':
        case 'name_module':
        case      'status':
        case         'url':
        case         'log':
            return $this->$attribute;
            break;
        default:
            return 'Não existe este atributo';
            break;
        }
    }


    /**
     * Função que faz setar dados nos atributos
     *
     * @param String $attribute {Nome do Atributo}
     * @param String $value     {Valor do Atributo}
     *
     * @return void
     * @access public
     */
    public function setAttribute($attribute, $value)
    {
        switch ($attribute) {
        case   'id_module':
        case 'name_module':
        case         'url':
            $this->$attribute = (string)$value;
            break;
        case 'status':
            if ($value instanceof Status && $value->exists()) {
                $this->$attribute = $value;
            }
            break;
        case 'log':
            $this->$attribute .= (string)$value . "\n";
            break;
        default:
            break;
        }
    }


    /**
     * Função que verifica se o objeto existe
     *
     * @return Boolean
     * @access public
     */
    public function exists()
    {
        return $this->_authentic;
    }


    /**
     * Função que constrói o objeto
     *
     * @param String $id_module {Id do Módulo a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_module)
    {
        $this->setAttribute('id_module', $id_module);
        $this->_load();
    }


    /**
     * Função que busca um módulo junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_module = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_module')));
        if ($cursor->rowCount() > 0) {
            $linha  = $cursor->fetch(PDO::FETCH_ASSOC);
            $status = new Status($linha['fk_id_status']);
            if ($status->exists()) {
                $this->setAttribute('id_module', $linha['pk_id_module']);
                $this->setAttribute('name_module', $linha['name_module']);
                $this->setAttribute('status', $status);
                $this->setAttribute('url', $linha['url']);
                $this->setAttribute('log', $linha['log']);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o módulo junto ao Banco de Dados
     *
     * @return boolean
     * @access private
     */
    public function save()
    {
        $retorno = 0;
        $module  = new Modules($this->getAttribute('id_module'));
        if (!$module->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
            ' (pk_id_module, fk_id_status, name_module, url, log) '.
            'VALUES (?, ?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_module'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('name_module'),
                          $this->getAttribute('url'),
                          $this->getAttribute('log'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'name_module = ?, fk_id_status = ?, url = ?, log = ? '.
            'WHERE pk_id_module = ?;';
            $values     = array($this->getAttribute('name_module'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('url'),
                          $this->getAttribute('log'),
                          $this->getAttribute('id_module'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca módulos junto ao Banco de Dados por parâmetros
     *
     * @param Integer $begin       {Início de onde deve buscar}
     * @param Integer $end         {Quantidade que deve buscar}
     * @param Integer $order       {Ordenação dos resultados}
     * @param String  $status      {Status dos módulos a buscar}
     * @param String  $name_module {Nome do módulo a buscar}
     * @param String  $url         {Url do módulo a buscar}
     *
     * @return Modules
     * @access private
     */
    public static function getModules(
        $begin,
        $end,
        $order = '',
        $status = '',
        $name_module = '',
        $url = ''
    ) {
        $sql    = 'SELECT pk_id_module FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($status instanceof Status) {
            $sql     .= ' AND fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if (!empty($name_module)) {
            $sql     .= ' AND name_module LIKE ?';
            $values[] = $name_module . '%';
        }
        if (!empty($url)) {
            $sql     .= ' AND url LIKE ?';
            $values[] = $url . '%';
        }
        if (!empty($order)) {
            $sql     .= ' ORDER BY ' . $order;
        }
        if (is_numeric($begin) && is_numeric($end) && $end) {
            $sql .= ' LIMIT ' . $begin . ', ' . $end;
        }
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute($values);
        if ($cursor->rowCount() > 0) {
            $retorno = array();
            while ($linha = $cursor->fetch(PDO::FETCH_ASSOC)) {
                $modulo = new Modules($linha['pk_id_module']);
                if ($modulo->exists()) {
                    $retorno[] = $modulo;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um módulo junto ao Banco de Dados
     *
     * @param Modules $module {Módulo a ser excluído}
     *
     * @return boolean
     * @access private
     */
    public static function removeModule(Modules $module)
    {
        $retorno = 0;
        if ($module->exists() && $module->getAttribute('id_module') != '') {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_module = ?;';
            $values     = array($module->getAttribute('id_module'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }



}
?>