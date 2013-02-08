<?php
/**
 * Arquivo que traz a classe de Conteúdo Extra do CMS
 * 
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Contents
 * @name     ExtraContents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Conteúdo Extra do CMS
 * 
 * @category Classes
 * @package  Contents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class ExtraContents
{
    /**
     * @var String
     * @access protected
     */
    protected $name;
    /**
     * @var String
     * @access protected
     */
    protected $value;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_contents_extra';

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
        case  'name':
        case 'value':
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
        case  'name':
        case 'value':
            $this->$attribute = (string)$value;
            break;
        default:
            break;
        }
    }


    /**
     * Função que constrói o objeto
     * 
     * @param String   $name    {Nome do Conteúdo Extra a ser criado}
     * @param Contents $content {Conteúdo pai a ser criado}
     * 
     * @return void
     * @access public
     */
    public function __construct(Contents $content, $name)
    {
        $this->setAttribute('name', $name);
        if ($content->exists()) {
            $this->_load($content);
        }
    }


    /**
     * Função que busca um Conteúdo junto ao Banco de Dados
     * 
     * @param Contents $content {Conteúdo pai a ser criado}
     * 
     * @return void
     * @access private
     */
    private function _load(Contents $content)
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE fk_id_content = ? AND name = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($content->getAttribute('id_content'), $this->getAttribute('name')));
        if ($cursor->rowCount() > 0) {
            $linha = $cursor->fetch(PDO::FETCH_ASSOC);
            $this->setAttribute('name', $linha['name']);
            $this->setAttribute('value', $linha['value']);
        }
    }


    /**
     * Função que salva o Conteúdo Extra junto ao Banco de Dados
     * 
     * @param Contents $content {Conteúdo ao qual o Conteúdo Extra será associado}
     * 
     * @return boolean
     * @access public
     */
    public function save(Contents $content)
    {
        $retorno       = 0;
        $extra_content = new ExtraContents($content, $this->getAttribute('name'));
        if ($extra_content->getAttribute('value') == '') {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE . 
            ' (fk_id_content, name, value) VALUES (?, ?, ?);';
            $values     = array($content->getAttribute('id_content'),
                          $this->getAttribute('name'),
                          html_entity_decode($this->getAttribute('value'), ENT_QUOTES, 'UTF-8'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'value = ? WHERE fk_id_content = ? AND name = ?;';
            $values     = array(html_entity_decode($this->getAttribute('value'), ENT_QUOTES, 'UTF-8'),
                          $content->getAttribute('id_content'),
                          $this->getAttribute('name'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca Conteúdos junto ao Banco de Dados por parâmetros
     * 
     * @param Contents $content {Conteúdo do Conteúdo Extra}
     * @param String   $name    {Nome do conteúdo extra a buscar}
     * @param String   $value   {Valor do Conteúdo extra a buscar}
     * 
     * @return ExtraContents
     * @access public
     */
    public static function getExtraContents(Contents $content, $name = '', $value = '')
    {
        if ($content->exists()) {
            $sql    = 'SELECT name FROM ' . self::TABLE . ' WHERE 1=1';
            $values = array();
            
            $sql     .= ' AND fk_id_content = ?';
            $values[] = $content->getAttribute('id_content');
            
            if (!empty($name)) {
                $sql     .= ' AND name LIKE ?';
                $values[] = $name . '%';
            }
            if (!empty($value)) {
                $sql     .= ' AND value = ?';
                $values[] = $value;
            }

            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $cursor->execute($values);
            if ($cursor->rowCount() > 0) {
                $retorno = array();
                while ($linha = $cursor->fetch(PDO::FETCH_ASSOC)) {
                    $content_extra = new ExtraContents($content, $linha['name']);
                    $retorno[]     = $content_extra;
                }
                return $retorno;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um conteúdo extra junto ao Banco de Dados
     * 
     * @param Contents      $content       {Conteúdo pai do Conteúdo Extra a ser excluída}
     * @param ExtraContents $extra_content {Conteúdo Extra a ser excluída}
     * 
     * @return boolean
     * @access public
     */
    public static function removeExtraContent(Contents $content, $extra_content = '')
    {
        $retorno = 0;
        if ($content->exists()) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'fk_id_content = ?';
            $values[] = $content->getAttribute('id_content');
            if ($extra_content instanceof ExtraContents && $extra_content->getAttribute('name') != '' && $extra_content->getAttribute('value') != '') {
                $sql     .= ' AND name = ? ';
                $values[] = $extra_content->getAttribute('name');
            }
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }




}
?>