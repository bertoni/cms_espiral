<?php
/**
 * Arquivo que traz a classe de Ações do CMS
 * 
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     Actions
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Ações do CMS
 * 
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Actions
{
    /**
     * @var String
     * @access protected
     */
    protected $id_action;
    /**
     * @var Actions
     * @access protected
     */
    protected $action_parent;
    /**
     * @var String
     * @access protected
     */
    protected $name_action;
    /**
     * @var String
     * @access protected
     */
    protected $description;
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
    const TABLE = 'tbl_actions';

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
        case     'id_action':
        case 'action_parent':
        case   'name_action':
        case   'description':
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
        case   'id_action':
        case 'name_action':
        case 'description':
            $this->$attribute = (string)$value;
            break;
        case 'action_parent':
            if ($value instanceof Actions && $value->exists()) {
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
     * @param String $id_action {Id da Ação a ser criado}
     * 
     * @return void
     * @access public
     */
    public function __construct($id_action)
    {
        $this->setAttribute('id_action', $id_action);
        $this->_load();
    }


    /**
     * Função que busca uma ação junto ao Banco de Dados
     * 
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_action = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_action')));
        if ($cursor->rowCount() > 0) {
            $linha = $cursor->fetch(PDO::FETCH_ASSOC);
            $this->setAttribute('id_action', $linha['pk_id_action']);
            $this->setAttribute('name_action', $linha['name_action']);
            $this->setAttribute('description', $linha['description']);
            $this->setAttribute('log', $linha['log']);
            $action = new Actions($linha['fk_id_action_parent']);
            if ($action->exists()) {
                $this->setAttribute('action_parent', $action);
            }
            $this->_authentic = true;
        }
    }


    /**
     * Função que salva a ação junto ao Banco de Dados
     * 
     * @return boolean
     * @access public
     */
    public function save()
    {
        $retorno = 0;
        $action  = new Actions($this->getAttribute('id_action'));
        if (!$action->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE . 
            ' (pk_id_action, fk_id_action_parent, name_action,' .
            'description, log) VALUES (?, ?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_action'),
                          ($this->getAttribute('action_parent') instanceof Actions ? $this->getAttribute('action_parent')->getAttribute('id_action') : null), 
                          $this->getAttribute('name_action'),
                          $this->getAttribute('description'),
                          $this->getAttribute('log'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'name_action = ?, fk_id_action_parent = ?, description = ?,' .
            'log = ? WHERE pk_id_action = ?;';
            $values     = array($this->getAttribute('name_action'), 
                          ($this->getAttribute('action_parent') instanceof Actions ? $this->getAttribute('action_parent')->getAttribute('id_action') : null),
                          $this->getAttribute('description'),
                          $this->getAttribute('log'),
                          $this->getAttribute('id_action'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca ações junto ao Banco de Dados por parâmetros
     * 
     * @param Integer $begin         {Início de onde deve buscar}
     * @param Integer $end           {Quantidade que deve buscar}
     * @param Integer $order         {Ordenação dos resultados}
     * @param String  $name_action   {Nome da Ação a buscar}
     * @param String  $action_parent {Ação pai}
     * 
     * @return Actions
     * @access public
     */
    public static function getActions($begin, $end, $order = '', $name_action = '', $action_parent = null)
    {
        $sql    = 'SELECT pk_id_action FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if (!empty($name_action)) {
            $sql     .= ' AND name_action LIKE ?';
            $values[] = $name_action . '%';
        }
        if ($action_parent instanceof Actions) {
            $sql     .= ' AND fk_id_action_parent = ?';
            $values[] = trim($action_parent->getAttribute('id_action'));
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
                $action = new Actions($linha['pk_id_action']);
                if ($action->exists()) {
                    $retorno[] = $action;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui uma ação junto ao Banco de Dados
     * 
     * @param Actions $action {Ação a ser excluída}
     * 
     * @return boolean
     * @access public
     */
    public static function removeAction(Actions $action)
    {
        $retorno = 0;
        if ($action->exists() && $action->getAttribute('id_action') != '') {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_action = ?;';
            $values     = array($action->getAttribute('id_action'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    
    


}
?>