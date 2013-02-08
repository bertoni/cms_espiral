<?php
/**
 * Arquivo que traz a classe de Conteúdos de Módulos do CMS
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     ContentsModule
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Conteúdos de Módulos do CMS
 *
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class ContentsModule
{
    /**
     * @var String
     * @access protected
     */
    protected $id_content_module;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var Modules
     * @access protected
     */
    protected $module;
    /**
     * @var String
     * @access protected
     */
    protected $name_content_module;
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
     * @access protected
     */
    protected $visible;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_contents_modules';

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
        case   'id_content_module':
        case 'name_content_module':
        case              'status':
        case              'module':
        case                 'url':
        case                 'log':
        case             'visible':
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
        case   'id_content_module':
        case 'name_content_module':
        case                 'url':
            $this->$attribute = (string)$value;
            break;
        case 'visible':
            $this->$attribute = (int)$value;
            break;
        case 'status':
            if ($value instanceof Status && $value->exists()) {
                $this->$attribute = $value;
            }
            break;
        case 'module':
            if ($value instanceof Modules && $value->exists()) {
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
     * @param String $id_content_module {Id do Conteúdo do Módulo a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_content_module)
    {
        $this->setAttribute('id_content_module', $id_content_module);
        $this->_load();
    }


    /**
     * Função que busca um conteúdo do módulo junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE .
                      ' WHERE pk_id_content_module = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_content_module')));
        if ($cursor->rowCount() > 0) {
            $linha  = $cursor->fetch(PDO::FETCH_ASSOC);
            $status = new Status($linha['fk_id_status']);
            $module = new Modules($linha['fk_id_module']);
            if ($status->exists() && $module->exists()) {
                $this->setAttribute(
                    'id_content_module',
                    $linha['pk_id_content_module']
                );

                $this->setAttribute(
                    'name_content_module',
                    $linha['name_content_module']
                );

                $this->setAttribute('status', $status);
                $this->setAttribute('module', $module);
                $this->setAttribute('url', $linha['url']);
                $this->setAttribute('log', $linha['log']);
                $this->setAttribute('visible', $linha['visible']);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o conteúdo do módulo junto ao Banco de Dados
     *
     * @return boolean
     * @access private
     */
    public function save()
    {
        $retorno     = 0;
        $contModule  = new ContentsModule($this->getAttribute('id_content_module'));
        if (!$contModule->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
            ' (pk_id_content_module, fk_id_module, fk_id_status, name_content_module,
             url, log, visible) VALUES (?, ?, ?, ?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_content_module'),
                          $this->getAttribute('module')->getAttribute('id_module'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('name_content_module'),
                          $this->getAttribute('url'),
                          $this->getAttribute('log'),
                          $this->getAttribute('visible'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'name_content_module = ?, fk_id_module = ?, fk_id_status = ?, url = ?,
            log = ?, visible = ? WHERE pk_id_content_module = ?;';
            $values     = array($this->getAttribute('name_content_module'),
                          $this->getAttribute('module')->getAttribute('id_module'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('url'),
                          $this->getAttribute('log'),
                          $this->getAttribute('visible'),
                          $this->getAttribute('id_content_module'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca um conteúdo do módulo
     *
     * @param Integer $begin               {Início de onde deve buscar}
     * @param Integer $end                 {Quantidade que deve buscar}
     * @param Integer $order               {Ordenação dos resultados}
     * @param String  $status              {Status dos conteúdos dos módulos}
     * @param String  $module              {Módulo dos conteúdos dos módulos}
     * @param String  $name_content_module {Nome do conteúdo do módulo a buscar}
     * @param String  $url                 {Url do conteúdo do módulo a buscar}
     * @param Boolean $visible             {Define a visibilidade do conteúdo do módulo a buscar}
     *
     * @return ContentsModule
     * @access private
     */
    public static function getContentsModules(
        $begin,
        $end,
        $order = '',
        $status = '',
        $module = '',
        $name_content_module = '',
        $url = '',
        $visible = ''
    ) {
        $sql    = 'SELECT pk_id_content_module FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($status instanceof Status) {
            $sql     .= ' AND fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if ($module instanceof Modules) {
            $sql     .= ' AND fk_id_module = ?';
            $values[] = $module->getAttribute('id_module');
        }
        if (!empty($name_content_module)) {
            $sql     .= ' AND name_content_module LIKE ?';
            $values[] = $name_content_module . '%';
        }
        if (!empty($url)) {
            $sql     .= ' AND url LIKE ?';
            $values[] = $url . '%';
        }
        if (!empty($visible)) {
            $sql     .= ' AND visible = ?';
            $values[] = $visible;
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
                $conteudoModulo = new ContentsModule($linha['pk_id_content_module']);
                if ($conteudoModulo->exists()) {
                    $retorno[] = $conteudoModulo;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um Conteúdo de Módulo junto ao Banco de Dados
     *
     * @param ContentsModule $content_module {Conteúdo de Módulo a ser excluído}
     *
     * @return boolean
     * @access private
     */
    public static function removeContentModule(ContentsModule $content_module)
    {
        $retorno = 0;
        if ($content_module->exists()
            && $content_module->getAttribute('id_content_module') != ''
        ) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_content_module = ?;';
            $values     = array($content_module->getAttribute('id_content_module'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }




}
?>