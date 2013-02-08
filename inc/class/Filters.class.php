<?php
/**
 * Arquivo que traz a classe de Filtros do CMS
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Filters
 * @name     Filters
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Filtros do CMS
 *
 * @category Classes
 * @package  Filters
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Filters
{
    /**
     * @var String
     * @access protected
     */
    protected $id_filter;
    /**
     * @var Filters
     * @access protected
     */
    protected $filter_parent;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var String
     * @access protected
     */
    protected $name_filter;
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
    const TABLE = 'tbl_filters';

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
        case     'id_filter':
        case   'name_filter':
        case 'filter_parent':
        case        'status':
        case           'log':
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
        case   'id_filter':
        case 'name_filter':
            $this->$attribute = (string)$value;
            break;
        case 'status':
            if ($value instanceof Status && $value->exists()) {
                $this->$attribute = $value;
            }
            break;
        case 'filter_parent':
            if ($value instanceof Filters && $value->exists()) {
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
     * @param String $id_filter {Id do Filtro a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_filter)
    {
        $this->setAttribute('id_filter', $id_filter);
        $this->_load();
    }


    /**
     * Função que busca um filtro junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_filter = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_filter')));
        if ($cursor->rowCount() > 0) {
            $linha  = $cursor->fetch(PDO::FETCH_ASSOC);
            $status = new Status($linha['fk_id_status']);
            if ($status->exists()) {
                $filter_parent = new Filters( $linha['fk_id_filter_parent']);
                if (!$filter_parent->exists()) {
                    $filter_parent = '';
                }
                $this->setAttribute('id_filter', $linha['pk_id_filter']);
                $this->setAttribute('name_filter', $linha['name_filter']);
                $this->setAttribute('filter_parent', $filter_parent);
                $this->setAttribute('status', $status);
                $this->setAttribute('log', $linha['log']);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o filtro junto ao Banco de Dados
     *
     * @return boolean
     * @access private
     */
    public function save()
    {
        $retorno = 0;
        $filter  = new Filters($this->getAttribute('id_filter'));
        if (!$filter->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
            ' (pk_id_filter, fk_id_filter_parent, fk_id_status, name_filter, log) '.
            'VALUES (?, ?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_filter'),
                          ($this->getAttribute('filter_parent') instanceof Filters ? 
                          $this->getAttribute('filter_parent')->getAttribute('id_filter') : 
                          null),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('name_filter'),
                          $this->getAttribute('log'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'name_filter = ?, fk_id_filter_parent = ?, fk_id_status = ?, log = ? '.
            'WHERE pk_id_filter = ?;';
            $values     = array($this->getAttribute('name_filter'),
                          ($this->getAttribute('filter_parent') instanceof Filters ? 
                          $this->getAttribute('filter_parent')->getAttribute('id_filter') : 
                          null),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('log'),
                          $this->getAttribute('id_filter'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca filtros junto ao Banco de Dados por parâmetros
     *
     * @param Integer $begin         {Início de onde deve buscar}
     * @param Integer $end           {Quantidade que deve buscar}
     * @param Integer $order         {Ordenação dos resultados}
     * @param String  $status        {Status dos módulos a buscar}
     * @param String  $name_filter   {Nome do filtro a buscar}
     * @param String  $filter_parent {Filtro a que ele está associado a buscar}
     * se $filter_parent for igual a um objeto irá buscar filtros que sejam filhos deste
     * se $filter_parent for igual a null irá buscar buscar filtros que não tenham pai
     * se $filter_parent for igual a 0 irá buscar filtros que sejam filhos de outros filtros
     * se $filter_parent for igual a TRUE irá buscar filtros que sejam pais de outros filtros
     *
     * @return Filters
     * @access private
     */
    public static function getFilters(
        $begin,
        $end,
        $order = '',
        $status = '',
        $name_filter = '',
        $filter_parent = ''
    ) {
        $sql    = 'SELECT pk_id_filter FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($status instanceof Status) {
            $sql     .= ' AND fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if ($filter_parent instanceof Filters) {
            $sql     .= ' AND fk_id_filter_parent = ?';
            $values[] = $filter_parent->getAttribute('id_filter');
        } elseif (is_null($filter_parent)) {
            $sql .= ' AND fk_id_filter_parent IS NULL';
        } elseif ($filter_parent === 0) {
            $sql .= ' AND fk_id_filter_parent IS NOT NULL';
        } elseif ($filter_parent === true) {
             $sql .= ' AND pk_id_filter IN (SELECT ffi.fk_id_filter_parent FROM ' . self::TABLE . ' ffi WHERE ffi.fk_id_filter_parent = ' . self::TABLE . '.pk_id_filter)';
        }
        if (!empty($name_filter)) {
            $sql     .= ' AND name_filter LIKE ?';
            $values[] = $name_filter . '%';
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
                $filter = new Filters($linha['pk_id_filter']);
                if ($filter->exists()) {
                    $retorno[] = $filter;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um filtro junto ao Banco de Dados
     *
     * @param Filters $filter {Filtro a ser excluído}
     *
     * @return boolean
     * @access private
     */
    public static function removeFilter(Filters $filter)
    {
        $retorno = 0;
        if ($filter->exists() && $filter->getAttribute('id_filter') != '') {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_filter = ?;';
            $values     = array($filter->getAttribute('id_filter'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }



}
?>