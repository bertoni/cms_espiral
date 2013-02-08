<?php
/**
 * Arquivo que traz a classe de Conteúdo do CMS
 * 
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Contents
 * @name     Contents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Conteúdo do CMS
 * 
 * @category Classes
 * @package  Contents
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Contents
{
    /**
     * @var Integer
     * @access protected
     */
    protected $id_content;
    /**
     * @var TypeContents
     * @access protected
     */
    protected $type_content;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var ExtraContents
     * @access protected
     */
    protected $extra_contents = array();
    /**
     * @var Integer
     * @access protected
     */
    protected $date_creation;
    /**
     * @var Integer
     * @access protected
     */
    protected $date_publication;
    /**
     * @var Integer
     * @access protected
     */
    protected $date_expiration;
    /**
     * @var String
     * @access protected
     */
    protected $title;
    /**
     * @var String
     * @access protected
     */
    protected $name;
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
    const TABLE = 'tbl_contents';
    /**
     * @var String
     * @access public
     */
    const TABLE_R_CONTENTS = 'tbl_contents_r_contents';

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
        case       'id_content':
        case     'type_content':
        case           'status':
        case    'date_creation':
        case 'date_publication':
        case  'date_expiration':
        case            'title':
        case             'name':
        case              'log':
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
        case       'id_content':
        case    'date_creation':
        case 'date_publication':
        case  'date_expiration':
            $this->$attribute = (int)$value;
            break;
        case 'title':
        case 'name':
            $this->$attribute = (string)$value;
            break;
        case 'type_content':
            if ($value instanceof TypeContents && $value->exists()) {
                $this->$attribute = $value;
            }
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
     * Função que busca os Conteúdos Extras associados ao Conteúdo
     * 
     * @param String $name {Nome do Conteúdo Extra}
     * 
     * @return ExtraContents
     * @access public
     */
    public function getExtraContent($name)
    {
        if (count($this->extra_contents) == 0) {
            $extras = ExtraContents::getExtraContents($this);
            if ($extras && count($extras) > 0) {
                foreach ($extras as $extra) {
                    $this->extra_contents[$extra->getAttribute('name')] = $extra->getAttribute('value');
                }
            }
        }
        if (isset($this->extra_contents[$name])) {
            return $this->extra_contents[$name];
        } else {
            return false;
        }
    }


    /**
     * Função que seta um Conteúdos Extras associados ao Conteúdo
     * 
     * @param String $name  {Nome do Conteúdo Extra}
     * @param String $value {Valor do Conteúdo Extra}
     * 
     * @return void
     * @access public
     */
    public function setExtraContent($name, $value)
    {
        $extra_content = new ExtraContents($this, $name);
        $extra_content->setAttribute('value', $value);
        $this->extra_contents[$name] = $extra_content;
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
     * @param String $id_content {Id do Conteúdo a ser criado}
     * 
     * @return void
     * @access public
     */
    public function __construct($id_content)
    {
        $this->setAttribute('id_content', $id_content);
        $this->_load();
    }


    /**
     * Função que busca um Conteúdo junto ao Banco de Dados
     * 
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_content = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_content')));
        if ($cursor->rowCount() > 0) {
            $linha        = $cursor->fetch(PDO::FETCH_ASSOC);
            $type_content = new TypeContents($linha['fk_id_type_content']);
            $status       = new Status($linha['fk_id_status']);
            if ($type_content->exists() && $status->exists()) {
                $this->setAttribute('id_content', $linha['pk_id_content']);
                $this->setAttribute('type_content', $type_content);
                $this->setAttribute('status', $status);
                $this->setAttribute('date_creation', $linha['date_creation']);
                $this->setAttribute('date_publication', $linha['date_publication']);
                $this->setAttribute('date_expiration', $linha['date_expiration']);
                $this->setAttribute('title', $linha['title']);
                $this->setAttribute('name', $linha['name']);
                $this->setAttribute('log', $linha['log']);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o Conteúdo junto ao Banco de Dados
     * 
     * @return boolean
     * @access public
     */
    public function save()
    {
        $retorno = 0;
        $content = new Contents($this->getAttribute('id_content'));
        if (!$content->exists()) {
            // INSERT
            $this->name = $this->_generationUniqueName($this->getAttribute('title'));
            $sql        = 'INSERT INTO ' . self::TABLE . 
            ' (fk_id_status, fk_id_type_content, date_creation, ' . 
            ' date_publication, date_expiration, title, name, log) VALUES ' .
            ' (?, ?, ?, ?, ?, ?, ?, ?);';
            $values     = array($this->getAttribute('status')->getAttribute('id_status'), 
                          $this->getAttribute('type_content')->getAttribute('id_type_content'),
                          mktime(),
                          $this->getAttribute('date_publication'),
                          $this->getAttribute('date_expiration'),
                          $this->getAttribute('title'),
                          $this->getAttribute('name'),
                          $this->getAttribute('log'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $connection->inTransaction();
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
            if ($retorno) {
                $this->setAttribute('id_content', $connection->getLastId());
                if (count($this->extra_contents) > 0) {
                    ExtraContents::removeExtraContent($this);
                    foreach ($this->extra_contents as $extra_content) {
                        if (!$extra_content->save($this)) {
                            $retorno = 0;
                        }
                    }
                }
            }
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'fk_id_status = ?, fk_id_type_content = ?, ' .
            'date_publication = ?, date_expiration = ?, ' .
            'title = ?, log = ? WHERE pk_id_content = ?;';
            $values     = array($this->getAttribute('status')->getAttribute('id_status'), 
                          $this->getAttribute('type_content')->getAttribute('id_type_content'),
                          $this->getAttribute('date_publication'),
                          $this->getAttribute('date_expiration'),
                          $this->getAttribute('title'),
                          $this->getAttribute('log'),
                          $this->getAttribute('id_content'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $connection->inTransaction();
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
            if ($retorno) {
                if (count($this->extra_contents) > 0) {
                    ExtraContents::removeExtraContent($this);
                    foreach ($this->extra_contents as $extra_content) {
                        if (!$extra_content->save($this)) {
                            $retorno = 0;
                        }
                    }
                }
            }
        }
        if ($retorno) {
            $connection->commit();
        } else {
            $connection->rollBack();
        }
        return $retorno;
    }


    /**
     * Função que gera o nome único do conteúdo
     * 
     * @param String $name      {Name a ser gerado}
     * @param String $increment {String a ser incrementado no name do conteúdo}
     * 
     * @return String
     * @access private
     */
    private function _generationUniqueName($name, $increment = '')
    {
        if (empty($increment)) {
            $name = explode(' ', $name);
            $max  = count($name);
            
        	$final_name = '';
        	for($i=0; $i<$max; $i++){
        	    $name[$i] = clearEmphasis($name[$i]);
        	    if (!$i) {
        		    $final_name .= $name[$i];
        	    } else {
        	        $final_name .= '-' . $name[$i];
        	    }
    	    }
        } else {
            $final_name = $name.$increment;
        }
        
    	$result = $this->checkUniqueName($final_name);
    	if ($result == false) {
    		return $final_name;
    	} else {
    		return $this->_generationUniqueName($final_name, date('s'));
    	}
    }


    /**
     * Função que verifica se o nome do conteúdo já existe
     * 
     * @param String $name {Name a ser pesquisado}
     * 
     * @return Boolean
     * @access public
     */
    public function checkUniqueName($name)
    {
        $sql        = 'SELECT name FROM ' . self::TABLE . ' WHERE name = ?';
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($name));
        if ($cursor->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Função que busca Conteúdos junto ao Banco de Dados por parâmetros
     * 
     * IMPORTANTE: o campo $search é um array e terá esta estrutura:
     * $search = array('sql' => 'campo = ? AND campo LIKE ?', 'values' => array('texto', '%outro_texto%'))
     * 
     * @param Integer $begin        {Início de onde deve buscar}
     * @param Integer $end          {Quantidade que deve buscar}
     * @param Integer $order        {Ordenação dos resultados}
     * @param String  $status       {Status do Conteúdo}
     * @param String  $type_content {Tipo de Conteúdo do Conteúdo}
     * @param Array   $search       {Campos a serem buscados}
     * 
     * @return Contents
     * @access public
     */
    public static function getContents($begin, $end, $order = '', $status = '', $type_content = '', $search = array())
    {
        $sql    = 'SELECT DISTINCT ' . self::TABLE . '.pk_id_content FROM ' . self::TABLE . ', ';
        $sql   .= ExtraContents::TABLE . ' WHERE 1=1';
        $values = array();
        $sql   .= ' AND ' . self::TABLE . '.pk_id_content = ' . ExtraContents::TABLE . '.fk_id_content';
        if ($status instanceof Status) {
            $sql     .= ' AND ' . self::TABLE . '.fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if ($type_content instanceof TypeContents) {
            $sql     .= ' AND ' . self::TABLE . '.fk_id_type_content = ?';
            $values[] = $type_content->getAttribute('id_type_content');
        }
        if (is_array($search) && isset($search['sql']) && isset($search['values']) && count($search) > 0 && 
            substr_count($search['sql'], '?') == count($search['values'])) {
            $sql .= ' ' . $search['sql'];
            foreach ($search['values'] as $val) {
                $values[] = $val;
            }
        }
        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
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
                $content = new Contents($linha['pk_id_content']);
                if ($content->exists()) {
                    $retorno[] = $content;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um conteúdo junto ao Banco de Dados
     * 
     * @param Contents $content {Conteúdo a ser excluída}
     * 
     * @return boolean
     * @access public
     */
    public static function removeContent(Contents $content)
    {
        $retorno = 0;
        if ($content->exists()) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_content = ?;';
            $values     = array($content->getAttribute('id_content'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca todos os campos da tabela
     * 
     * @return Array
     * @access public
     */
    public static function getFieldsBytable()
    {
        $sql = 'SHOW COLUMNS FROM ' . self::TABLE;
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute();
        if ($cursor->rowCount() > 0) {
            $retorno = array();
            while ($linha = $cursor->fetch(PDO::FETCH_ASSOC)) {
                $retorno[] = $linha;
            }
            return $retorno;
        } else {
            return false;
        }
    }
    

    /**
     * Função que verifica a relação de um conteúdo com outro
     *
     * @param Contents $content_parent {Conteúdo pai}
     * @param Contents $content_child  {Conteúdo filho}
     *
     * @return boolean
     * @access public
     */
    public static function checkContentsRContents(Contents $content_parent, Contents $content_child)
    {
        $sql    = 'SELECT * FROM ' . self::TABLE_R_CONTENTS . ' WHERE '.
        'fk_id_content_parent = ? AND fk_id_content_child = ?';
        $values = array($content_parent->getAttribute('id_content'),
                        $content_child->getAttribute('id_content'));

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
     * Função que grava a relação de um conteúdo com outro
     *
     * @param Contents $content_parent {Conteúdo pai}
     * @param Contents $content_child  {Conteúdo filho}
     * @param boolean  $action         {True para incluir e False para excluir}
     *
     * @return boolean
     * @access public
     */
    public static function setRelContentsRContents(Contents $content_parent, Contents $content_child, $action)
    {
        if (is_bool($action)) {
            $ret        = false;
            $retorno    = Contents::checkContentsRContents($content_parent, $content_child);
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $values     = array($content_parent->getAttribute('id_content'),
                                $content_child->getAttribute('id_content'));
            
            if ($action) {
                if (!$retorno) {
                    $sql     = 'INSERT INTO ' . self::TABLE_R_CONTENTS .
                    ' (fk_id_content_parent, fk_id_content_child) ' .
                    ' VALUES (?, ?)';
                    $cursor  = $connection->prepareQuery($sql);
                    $retorno = $cursor->execute($values);
                    if ($retorno) {
                        $ret = true;
                    }
                }
            } else {
                if ($retorno) {
                    $sql     = 'DELETE FROM ' . self::TABLE_R_CONTENTS .
                    ' WHERE fk_id_content_parent = ? AND fk_id_content_child = ?';
                    $cursor  = $connection->prepareQuery($sql);
                    $retorno = $cursor->execute($values);
                    if ($retorno) {
                        $ret = true;
                    }
                }
            }
            return $ret;
        } else {
            return false;
        }
    }


    /**
     * Função que busca Conteúdos que possuam relação com outros conteúdos
     *
     * @param Contents    $content     {Conteúdo a ser buscado}
     * @param boolean     $action      {True para buscar relaçṍes com filhos, False para buscar relações com pais}
     * @param TypeContent $typeContent {Tipo de Conteúdo buscado}
     *
     * @return Contents
     * @access public
     */
    public static function getContentsRelationship(Contents $content, $action, $typeContent = '')
    {
        if ($content->exists()) {
            if ($action) {
                $sql  = 'SELECT rel.fk_id_content_child as pk_id_content FROM ' . self::TABLE_R_CONTENTS . ' rel, ';
                $sql .= self::TABLE . ' co  WHERE rel.fk_id_content_parent = ?';
                $sql .= ' AND rel.fk_id_content_child = co.pk_id_content';
                $values[] = $content->getAttribute('id_content');
                if ($typeContent instanceof TypeContents && $typeContent->exists()) {
                    $sql     .= ' AND co.fk_id_type_content = ?';
                    $values[] = $typeContent->getAttribute('id_type_content');
                }
            } else {
                $sql  = 'SELECT rel.fk_id_content_parent as pk_id_content FROM ' . self::TABLE_R_CONTENTS . ' rel, ';
                $sql .= self::TABLE . ' co  WHERE rel.fk_id_content_child = ?';
                $sql .= ' AND rel.fk_id_content_parent = co.pk_id_content';
                $values[] = $content->getAttribute('id_content');
                if ($typeContent instanceof TypeContents && $typeContent->exists()) {
                    $sql     .= ' AND co.fk_id_type_content = ?';
                    $values[] = $typeContent->getAttribute('id_type_content');
                }
            }
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $cursor->execute($values);
            if ($cursor->rowCount() > 0) {
                $retorno = array();
                while ($linha = $cursor->fetch(PDO::FETCH_ASSOC)) {
                    $contents = new Contents($linha['pk_id_content']);
                    if ($contents->exists()) {
                        $retorno[] = $contents;
                    }
                }
                return $retorno;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


}
?>