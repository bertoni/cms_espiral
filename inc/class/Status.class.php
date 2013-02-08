<?php
/**
 * Arquivo que traz a classe de Status da aplicação
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de todos possíveis status da aplicação
 *
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */
final class Status
{
    /**
     * @var String
     * @access protected
     */
    protected $id_status;
    /**
     * @var String
     * @access protected
     */
    protected $name_status;
    /**
     * @var Boolean
     * @access private
     */
    private $_authentic = false;
    /**
     * @var String
     * @access public
     */
    const TABLE = 'tbl_status';

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
        case 'id_status':
            return $this->$attribute;
            break;
        case 'name_status':

            switch ($this->$attribute) {
                case "aguardando aprovação":
                    return '<span class="status-yellow">aguardando aprovação</span>';
                    break;
                case "inativo(a)":
                    return '<span class="status-red">inativo(a)</span>';
                    break;
                default:
                    return $this->$attribute;
                    break;
            }
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
        if ($attribute == 'id_status') {
            $this->$attribute = $value;
        }
        if ($attribute == 'name_status') {
            $this->$attribute = (string)$value;
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
     * @param String $id_status {Id do Status a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_status)
    {
        $this->setAttribute('id_status', $id_status);
        $this->_load();
    }


    /**
     * Função que busca um status junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_status = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_status')));
        if ($cursor->rowCount() > 0) {
            $linha = $cursor->fetch(PDO::FETCH_ASSOC);
            $this->setAttribute('id_status', $linha['pk_id_status']);
            $this->setAttribute('name_status', $linha['name_status']);
            $this->_authentic = true;
        }
    }


    /**
     * Função que busca status junto ao Banco de Dados
     *
     * @return Status
     * @access private
     */
    public static function getStatus()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $cursor     = $connection->prepareQuery('SELECT * FROM ' . self::TABLE);
        $cursor->execute();
        if ($cursor->rowCount() > 0) {
            $retorno = array();
            while ($linha = $cursor->fetch(PDO::FETCH_ASSOC)) {
                $status = new Status($linha['pk_id_status']);
                if ($status->exists()) {
                    $retorno[] = $status;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


}
?>