<?php
/**
 * Arquivo que traz a classe de Cidades do CMS
 * 
 * PHP Version 5.3
 *
 * @category Classes
 * @package  StateCities
 * @name     Cities
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Cidades do CMS
 * 
 * @category Classes
 * @package  StateCities
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
class Cities
{
    /**
     * @var Integer
     * @access protected
     */
    protected $id_city;
    /**
     * @var String
     * @access protected
     */
    protected $state;
    /**
     * @var String
     * @access protected
     */
    protected $name_city;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_cities';

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
        case   'id_city':
        case     'state':
        case 'name_city':
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
        case     'state':
        case 'name_city':
            $this->$attribute = (string)$value;
            break;
        case 'id_city':
            $this->$attribute = (int)$value;
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
     * @param Integer $id_city {Id da Cidade a ser criado}
     * 
     * @return void
     * @access public
     */
    public function __construct($id_city)
    {
        $this->setAttribute('id_city', $id_city);
        $this->_load();
    }


    /**
     * Função que busca uma cidade junto ao Banco de Dados
     * 
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_city = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_city')));
        if ($cursor->rowCount() > 0) {
            $linha = $cursor->fetch(PDO::FETCH_ASSOC);
            $this->setAttribute('id_city', $linha['pk_id_city']);
            $this->setAttribute('state', $linha['state']);
            $this->setAttribute('name_city', $linha['name_city']);
            $this->_authentic = true;
        }
    }


    /**
     * Função que busca cidades junto ao Banco de Dados por parâmetros
     * 
     * @param Integer $begin     {Início de onde deve buscar}
     * @param Integer $end       {Quantidade que deve buscar}
     * @param Integer $order     {Ordenação dos resultados}
     * @param String  $state     {Nome da Ação a buscar}
     * @param String  $name_city {Ação pai}
     * 
     * @return Actions
     * @access public
     */
    public static function getCities($begin, $end, $order = '', $state = '', $name_city = '')
    {
        $sql    = 'SELECT pk_id_city FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if (!empty($name_city)) {
            $sql     .= ' AND name_city LIKE ?';
            $values[] = $name_city . '%';
        }
        if (!empty($state)) {
            $sql     .= ' AND state = ?';
            $values[] = $state;
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
                $city = new Cities($linha['pk_id_city']);
                if ($city->exists()) {
                    $retorno[] = $city;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


}
?>