<?php
/**
 * Arquivo que traz a classe de Campos do CMS
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     FormFields
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Campos do CMS
 *
 * @category Classes
 * @package  Filters
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class FormFields
{
    /**
     * @var String
     * @access protected
     */
    protected $id_form_field;
    /**
     * @var Filters
     * @access protected
     */
    protected $filter;
    /**
     * @var String
     * @access protected
     */
    protected $name_form_field;
    /**
     * @var String
     * @access protected
     */
    protected $type_html;
    /**
     * @var String
     * @access protected
     */
    protected $function_js;
    /**
     * @var String
     * @access protected
     */
    protected $value_default;
    /**
     * @var String
     * @access protected
     */
    protected $class_css;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_form_fields';

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
        case   'id_form_field':
        case          'filter':
        case 'name_form_field':
        case       'type_html':
        case     'function_js':
        case   'value_default':
        case       'class_css':
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
        case   'id_form_field':
        case 'name_form_field':
        case       'type_html':
        case     'function_js':
        case   'value_default':
        case       'class_css':
            $this->$attribute = (string)$value;
            break;
        case 'filter':
            if ($value instanceof Filters && $value->exists()) {
                $this->$attribute = $value;
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
     * Função que constrói o objeto
     *
     * @param String $id_form_field {Id do Campo a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_form_field)
    {
        $this->setAttribute('id_form_field', $id_form_field);
        $this->_load();
    }


    /**
     * Função que busca um campo junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_form_field = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_form_field')));
        if ($cursor->rowCount() > 0) {
            $linha  = $cursor->fetch(PDO::FETCH_ASSOC);
            
            $filter = new Filters($linha['fk_id_filter']);
            if (!$filter->exists()) {
                $filter = '';
            }

            $this->setAttribute('id_form_field', $linha['pk_id_form_field']);
            $this->setAttribute('filter', $filter);
            $this->setAttribute('name_form_field', $linha['name_form_field']);
            $this->setAttribute('type_html', $linha['type_html']);
            $this->setAttribute('function_js', $linha['function_js']);
            $this->setAttribute('value_default', $linha['value_default']);
            $this->setAttribute('class_css', $linha['class_css']);
            $this->_authentic = true;
        }
    }


    /**
     * Função que salva o filtro junto ao Banco de Dados
     *
     * @return boolean
     * @access public
     */
    public function save()
    {
        $retorno = 0;
        $form_field = new FormFields($this->getAttribute('id_form_field'));
        if (!$form_field->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
            ' (pk_id_form_field, fk_id_filter, name_form_field, ' .
            'type_html, function_js, value_default, class_css) ' .
            'VALUES (?, ?, ?, ?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_form_field'),
                          ($this->getAttribute('filter') instanceof Filters ? $this->getAttribute('filter')->getAttribute('id_filter') : null),
                          $this->getAttribute('name_form_field'),
                          $this->getAttribute('type_html'),
                          $this->getAttribute('function_js'),
                          $this->getAttribute('value_default'),
                          $this->getAttribute('class_css'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'fk_id_filter = ?, name_form_field = ?, ' .
            'type_html = ?, function_js = ?, ' .
            'value_default = ?, class_css = ? ' .
            'WHERE pk_id_form_field = ?;';
            $values     = array(($this->getAttribute('filter') instanceof Filters ? $this->getAttribute('filter')->getAttribute('id_filter') : null),
                          $this->getAttribute('name_form_field'),
                          $this->getAttribute('type_html'),
                          $this->getAttribute('function_js'),
                          $this->getAttribute('value_default'),
                          $this->getAttribute('class_css'),
                          $this->getAttribute('id_form_field'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca campos junto ao Banco de Dados por parâmetros
     *
     * @param Integer $begin       {Início de onde deve buscar}
     * @param Integer $end         {Quantidade que deve buscar}
     * @param Integer $order       {Ordenação dos resultados}
     *
     * @return FormFields
     * @access public
     */
    public static function getFormFields($begin, $end, $order = '')
    {
        $sql    = 'SELECT pk_id_form_field FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
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
                $form_field = new FormFields($linha['pk_id_form_field']);
                if ($form_field->exists()) {
                    $retorno[] = $form_field;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um campo junto ao Banco de Dados
     *
     * @param FormFields $form_field {Campo a ser excluído}
     *
     * @return boolean
     * @access public
     */
    public static function removeFormFiled(FormFields $form_field)
    {
        $retorno = 0;
        if ($form_field->exists() && $form_field->getAttribute('id_form_field') != '') {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_form_field = ?;';
            $values     = array($form_field->getAttribute('id_form_field'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }



}
?>