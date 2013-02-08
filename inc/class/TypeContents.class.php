<?php
/**
 * Arquivo que traz a classe de Tipos de Conteúdos do CMS
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Contents
 * @name     TypeContents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Tipos de Conteúdos do CMS
 *
 * @category Classes
 * @package  Contents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class TypeContents
{
    /**
     * @var String
     * @access protected
     */
    protected $id_type_content;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var String
     * @access protected
     */
    protected $name_type_content;
    /**
     * @var String
     * @access protected
     */
    protected $log;
    /**
     * @var ConfigForm
     * @access protected
     */
    protected $config_form = array();
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_type_contents';
    /**
     * @var String
     * @access public
     */
    const TABLE_TYPES_R_ACTIONS = 'tbl_r_actions_type_contents';
    /**
     * @var String
     * @access public
     */
    const TABLE_TYPES_R_TYPES = 'tbl_type_contents_r_type_contents';
    

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
        case   'id_type_content':
        case            'status':
        case 'name_type_content':
        case               'log':
            return $this->$attribute;
            break;
        case 'config_form':
            $this->_getConfigForms();
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
        case   'id_type_content':
        case 'name_type_content':
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
        case 'config_form':
            if ($value instanceof ConfigForm && $value->exists()) {
                $this->config_form[] = $value;
            }
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
     * Função que limpa o array de configurações de formulários
     *
     * @return void
     * @access public
     */
    public function cleanConfigForms()
    {
        $this->config_form = array();
    }


    /**
     * Função que busca as configurações de formulários
     *
     * @return void
     * @access private
     */
    private function _getConfigForms()
    {
        if (count($this->config_form) == 0) {
            $this->cleanConfigForms();
            $arr_config = ConfigForm::getConfigForms('', '', 'order_show, name', $this);
            if ($arr_config && count($arr_config) > 0) {
                foreach ($arr_config as $config) {
                    $this->setAttribute('config_form', $config);
                }
            }
        }
    }


    /**
     * Função que constrói o objeto
     *
     * @param String $id_type_content {Id do Tipo de Conteúdo a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_type_content)
    {
        $this->setAttribute('id_type_content', $id_type_content);
        $this->_load();
    }


    /**
     * Função que busca um tipo de conteúdo junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE .
                      ' WHERE pk_id_type_content = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_type_content')));
        if ($cursor->rowCount() > 0) {
            $linha    = $cursor->fetch(PDO::FETCH_ASSOC);
            $status   = new Status($linha['fk_id_status']);
            if ($status->exists()) {
                $this->setAttribute('id_type_content', $linha['pk_id_type_content']);
                $this->setAttribute(
                    'name_type_content',
                    $linha['name_type_content']
                );
                $this->setAttribute('status', $status);
                $this->setAttribute('log', $linha['log']);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o tipo de conteúdo junto ao Banco de Dados
     *
     * @return boolean
     * @access public
     */
    public function save()
    {
        $retorno     = 0;
        $typeContent = new TypeContents($this->getAttribute('id_type_content'));
        if (!$typeContent->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
                          ' (pk_id_type_content, fk_id_status,' .
                          'name_type_content, log) VALUES (?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_type_content'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('name_type_content'),
                          $this->getAttribute('log'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
                          'name_type_content = ?, fk_id_status = ?, log = ?
                          WHERE pk_id_type_content = ?;';
            $values     = array($this->getAttribute('name_type_content'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('log'),
                          $this->getAttribute('id_type_content'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca tipos de conteúdos
     *
     * @param Integer $begin             {Início de onde deve buscar}
     * @param Integer $end               {Quantidade que deve buscar}
     * @param Integer $order             {Ordenação dos resultados}
     * @param String  $status            {Status dos conteúdos dos módulos}
     * @param String  $name_type_content {Nome do tipo de conteúdo a buscar}
     *
     * @return TypeContents
     * @access public
     */
    public static function getTypeContents(
        $begin,
        $end,
        $order = '',
        $status = '',
        $name_type_content = ''
    ) {
        $sql    = 'SELECT pk_id_type_content FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($status instanceof Status) {
            $sql     .= ' AND fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if (!empty($name_type_content)) {
            $sql     .= ' AND name_type_content LIKE ?';
            $values[] = $name_type_content . '%';
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
                $tipoConteudo = new TypeContents($linha['pk_id_type_content']);
                if ($tipoConteudo->exists()) {
                    $retorno[] = $tipoConteudo;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um tipo de Conteúdo junto ao Banco de Dados
     *
     * @param TypeContents $type_content {Tipo de Conteúdo a ser excluído}
     *
     * @return boolean
     * @access public
     */
    public static function removeTypeContent(TypeContents $type_content)
    {
        $retorno = 0;
        if ($type_content->exists()
            && $type_content->getAttribute('id_type_content') != ''
        ) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_type_content = ?;';
            $values     = array($type_content->getAttribute('id_type_content'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca as ações relacionadas com um tipo de conteúdo
     *
     * @param TypeContents $type_content {Tipo de Conteúdo a ser buscado}
     * @param Actions      $action       {Ação a ser buscada}
     *
     * @return boolean
     * @access public
     */
    public static function checkActionByTypeContent(
        TypeContents $type_content,
        Actions $action
    ) {
        if ($action->exists() && $type_content->exists()) {
            $sql = 'SELECT * FROM ' . self::TABLE_TYPES_R_ACTIONS . ' WHERE ' .
            'fk_id_action = ? AND fk_id_type_content = ?';
            $values = array($action->getAttribute('id_action'),
                            $type_content->getAttribute('id_type_content'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $cursor->execute($values);
            if ($cursor->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Função que grava a relação de um tipo de conteúdo com uma ação
     *
     * @param Actions $type_content {Tipo de conteúdo}
     * @param Actions $action       {Ação a ser verificada}
     * @param Integer $rule         {Permissão a ser gravada}
     *
     * @return boolean
     * @access public
     */
    public static function setActionByTypeContent(
        TypeContents $type_content,
        Actions $action, $rule
    ) {
        if ($action->exists() && $type_content->exists()) {
            $retorno = false;
            if ($rule == '1') {
                // INSERT
                $current = self::checkActionByTypeContent($type_content, $action);
                if (!$current) {
                    $sql    = 'INSERT INTO ' . self::TABLE_TYPES_R_ACTIONS .
                    ' (fk_id_action, fk_id_type_content) VALUES (?, ?);';
                    $values = array($action->getAttribute('id_action'),
                                    $type_content->getAttribute('id_type_content'));

                    $REGISTRY   = Registry::getInstance();
                    $connection = $REGISTRY->get('dbmysql');
                    $cursor     = $connection->prepareQuery($sql);
                    $retorno    = $cursor->execute($values);
                } else {
                    return false;
                }
            } else {
                // DELETE
                $sql        = 'DELETE FROM ' . self::TABLE_TYPES_R_ACTIONS .
                              ' WHERE fk_id_action = ? AND fk_id_type_content = ?;';
                $values     = array($action->getAttribute('id_action'),
                                    $type_content->getAttribute('id_type_content'));
                $REGISTRY   = Registry::getInstance();
                $connection = $REGISTRY->get('dbmysql');
                $cursor     = $connection->prepareQuery($sql);
                $retorno    = $cursor->execute($values);
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que verifica a relação de um tipo de conteúdo com outro
     *
     * @param TypeContents $type_content_parent {Tipo de conteúdo pai}
     * @param TypeContents $type_content_child  {Tipo de conteúdo filho}
     *
     * @return boolean
     * @access public
     */
    public static function checkTypeContentRTypeContent(TypeContents $type_content_parent, TypeContents $type_content_child)
    {
        $sql    = 'SELECT * FROM ' . self::TABLE_TYPES_R_TYPES . ' WHERE '.
        'fk_id_type_content_parent = ? AND fk_id_type_content_child = ?';
        $values = array($type_content_parent->getAttribute('id_type_content'),
                        $type_content_child->getAttribute('id_type_content'));

        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $cursor     = $connection->prepareQuery($sql);
        $retorno    = $cursor->execute($values);
        if ($cursor->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Função que grava a relação de um tipo de conteúdo com outro
     *
     * @param TypeContents $type_content_parent {Tipo de conteúdo pai}
     * @param TypeContents $type_content_child  {Tipo de conteúdo filho}
     * @param boolean      $action              {True para incluir e False para excluir}
     *
     * @return boolean
     * @access public
     */
    public static function setRelTypeContentTypeContent(TypeContents $type_content_parent, TypeContents $type_content_child, $action)
    {
        if (is_bool($action)) {
            $ret        = false;
            $retorno    = TypeContents::checkTypeContentRTypeContent($type_content_parent, $type_content_child);
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $values     = array($type_content_parent->getAttribute('id_type_content'),
                                $type_content_child->getAttribute('id_type_content'));
            
            if ($action) {
                if (!$retorno) {
                    $sql     = 'INSERT INTO ' . self::TABLE_TYPES_R_TYPES .
                    ' (fk_id_type_content_parent, fk_id_type_content_child) ' .
                    ' VALUES (?, ?)';
                    $cursor  = $connection->prepareQuery($sql);
                    $retorno = $cursor->execute($values);
                    if ($retorno) {
                        $ret = true;
                    }
                } else {
                    $ret = true;
                }
            } else {
                if ($retorno) {
                    $sql     = 'DELETE FROM ' . self::TABLE_TYPES_R_TYPES .
                    ' WHERE fk_id_type_content_parent = ? AND fk_id_type_content_child = ?';
                    $cursor  = $connection->prepareQuery($sql);
                    $retorno = $cursor->execute($values);
                    if ($retorno) {
                        $ret = true;
                    }
                } else {
                    $ret = true;
                }
            }
            return $ret;
        } else {
            return false;
        }
    }

}
?>