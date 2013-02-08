<?php
/**
 * Arquivo que traz a classe de Configurações de Campos
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Contents
 * @name     ConfigForm
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Configurações de Campos
 *
 * @category Classes
 * @package  Contents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class ConfigForm
{
    /**
     * @var FormFields
     * @access protected
     */
    protected $form;
    /**
     * @var String
     * @access protected
     */
    protected $name;
    /**
     * @var String
     * @access protected
     */
    protected $label;
    /**
     * @var Integer
     * @access protected
     */
    protected $max_lenght;
    /**
     * @var Boolean
     * @access protected
     */
    protected $required;
    /**
     * @var Integer
     * @access protected
     */
    protected $order_show;
    /**
     * @var String
     * @access protected
     */
    protected $use_with_filter;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_type_contents_config_form';

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
        case            'form':
        case            'name':
        case           'label':
        case      'max_lenght':
        case        'required':
        case      'order_show':
        case 'use_with_filter':
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
        case            'name':
        case           'label':
        case        'required':
        case 'use_with_filter':
            $this->$attribute = (string)$value;
            break;
        case 'max_lenght':
        case 'order_show':
            $this->$attribute = (int)$value;
            break;
        case 'form':
            if ($value instanceof FormFields && $value->exists()) {
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
     * @param FormFields $form       {Campo a ser utilizado}
     * @param String     $name       {Nome a ser utilizado}
     * @param String     $label      {Rótulo a ser utilizado}
     * @param Integer    $max_lenght {Tamanho máximo a ser utilizado}
     * @param Integer    $required   {Obrigatoriedade do campo a ser utilizado}
     * @param String     $use_form   {Define se o campo será utilizado como filtro a ser utilizado}
     *
     * @return void
     * @access public
     */
    public function __construct(FormFields $form, $name, $label, $max_lenght, $required, $order, $use_form)
    {
        if ($form->exists() && !empty($name) && !empty($label) && ($required != '')) {
            $this->setAttribute('form', $form);
            $this->setAttribute('name', $name);
            $this->setAttribute('label', $label);
            $this->setAttribute('max_lenght', $max_lenght);
            $this->setAttribute('required', $required);
            $this->setAttribute('order_show', $order);
            $this->setAttribute('use_with_filter', $use_form);
            $this->_authentic = true;
        }
    }


    /**
     * Função que salva a configuração do campo junto ao Banco de Dados
     *
     * @param TypeContents $type_content {Typo de conteúdo pai da configuração}
     *
     * @return boolean
     * @access private
     */
    public function save(TypeContents $type_content)
    {
        $retorno = false;
        if ($type_content->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
            ' (fk_id_type_content, fk_id_form_field, required, max_lenght, ' .
            'label, name, order_show, use_with_filter) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';
            $values     = array($type_content->getAttribute('id_type_content'),
                          $this->getAttribute('form')->getAttribute('id_form_field'),
                          $this->getAttribute('required'),
                          $this->getAttribute('max_lenght'),
                          $this->getAttribute('label'),
                          $this->getAttribute('name'),
                          $this->getAttribute('order_show'),
                          $this->getAttribute('use_with_filter'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca configurações de campos
     *
     * @param Integer      $begin        {Início de onde deve buscar}
     * @param Integer      $end          {Quantidade que deve buscar}
     * @param Integer      $order        {Ordenação dos resultados}
     * @param TypeContents $type_content {Tipo de conteúdo da configuração}
     *
     * @return ConfigForm
     * @access private
     */
    public static function getConfigForms($begin, $end, $order, TypeContents $type_content)
    {
        $sql    = 'SELECT * FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($type_content instanceof TypeContents && $type_content->exists()) {
            $sql     .= ' AND fk_id_type_content = ?';
            $values[] = $type_content->getAttribute('id_type_content');
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
                $form = new FormFields($linha['fk_id_form_field']);
                if ($form->exists()) {
                    $config_form = new ConfigForm($form, $linha['name'],
                    $linha['label'], $linha['max_lenght'], $linha['required'],
                    $linha['order_show'], $linha['use_with_filter']);
                    if ($config_form->exists()) {
                        $retorno[] = $config_form;
                    }
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui Configurações de Tipos de Conteúdos junto ao Banco de Dados
     *
     * @param TypeContents $type_content {Tipo de Conteúdo pai a ser excluído}
     *
     * @return boolean
     * @access private
     */
    public static function removeConfigForm(TypeContents $type_content)
    {
        $retorno = false;
        if ($type_content->exists()) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'fk_id_type_content = ?;';
            $values     = array($type_content->getAttribute('id_type_content'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }



}
?>
