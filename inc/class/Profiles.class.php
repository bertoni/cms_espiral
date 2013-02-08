<?php
/**
 * Arquivo que traz a classe de Perfis de Usuários
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Users
 * @name     Profiles
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Perfis de Usuários
 *
 * @category Classes
 * @package  Users
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Profiles
{
    /**
     * @var String
     * @access protected
     */
    protected $id_profile;
    /**
     * @var Profiles
     * @access protected
     */
    protected $profile_parent;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var String
     * @access protected
     */
    protected $profile;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_profiles';

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
        case     'id_profile':
        case 'profile_parent':
        case        'profile':
        case         'status':
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
        case 'id_profile':
        case    'profile':
            $this->$attribute = (string)$value;
            break;
        case 'status':
            if ($value instanceof Status && $value->exists()) {
                $this->$attribute = $value;
            }
            break;
        case 'profile_parent':
            if ($value instanceof Profiles && $value->exists()) {
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
     * @param String $id_profile {Id do Profile a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_profile)
    {
        $this->setAttribute('id_profile', $id_profile);
        $this->_load();
    }


    /**
     * Função que busca um perfil junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_profile = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_profile')));
        if ($cursor->rowCount() > 0) {
            $linha  = $cursor->fetch(PDO::FETCH_ASSOC);
            $status = new Status($linha['fk_id_status']);
            if ($status->exists()) {
                
                $parent = new Profiles($linha['fk_id_profile_parent']);
                if (!$parent->exists()) {
                    $parent = '';
                }
                
                $this->setAttribute('id_profile', $linha['pk_id_profile']);
                $this->setAttribute('profile', $linha['profile']);
                $this->setAttribute('status', $status);
                $this->setAttribute('profile_parent', $parent);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o conteúdo do perfil junto ao Banco de Dados
     *
     * @return boolean
     * @access private
     */
    public function save()
    {
        $retorno = 0;
        $profile = new Profiles($this->getAttribute('id_profile'));
        if (!$profile->exists()) {
            // INSERT
            $sql        = 'INSERT INTO ' . self::TABLE .
            ' (pk_id_profile, fk_id_status, fk_id_profile_parent, profile) VALUES (?, ?, ?, ?);';
            $values     = array($this->getAttribute('id_profile'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          ($this->getAttribute('profile_parent') instanceof Profiles ? $this->getAttribute('profile_parent')->getAttribute('id_profile') : null),
                          $this->getAttribute('profile'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        } else {
            // UPDATE
            $sql        = 'UPDATE ' . self::TABLE . ' SET ' .
            'fk_id_status = ?, profile = ?, fk_id_profile_parent = ? WHERE pk_id_profile = ?;';
            $values     = array(
                $this->getAttribute('status')->getAttribute('id_status'),
                $this->getAttribute('profile'),
                ($this->getAttribute('profile_parent') instanceof Profiles ? $this->getAttribute('profile_parent')->getAttribute('id_profile') : null),
                $this->getAttribute('id_profile')
            );
            
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


    /**
     * Função que busca perfis
     *
     * @param Integer $begin  {Início de onde deve buscar}
     * @param Integer $end    {Quantidade que deve buscar}
     * @param Integer $order  {Ordenação dos resultados}
     * @param String  $status {Status dos perfis}
     * @param String  $parent {Define o pai dos perfis a buscar}
     *
     * @return Profiles
     * @access private
     */
    public static function getProfiles($begin, $end, $order = '', $status = '', $parent = '')
    {
        $sql    = 'SELECT pk_id_profile FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($status instanceof Status) {
            $sql     .= ' AND fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if ($parent instanceof Profiles && $parent->exists()) {
            $sql     .= ' AND fk_id_profile_parent  = ?';
            $values[] = $parent->getAttribute('id_profile');
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
                $profile = new Profiles($linha['pk_id_profile']);
                if ($profile->exists()) {
                    $retorno[] = $profile;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um perfil junto ao Banco de Dados
     *
     * @param Profiles $profile {Perfil a ser excluído}
     *
     * @return boolean
     * @access private
     */
    public static function removeProfile(Profiles $profile)
    {
        $retorno = 0;
        if ($profile->exists()) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE ' .
            'pk_id_profile = ?;';
            $values     = array($profile->getAttribute('id_profile'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }

}
?>