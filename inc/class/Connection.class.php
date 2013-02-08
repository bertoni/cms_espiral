<?php
/**
 * Arquivo que traz a classe de abstração de dados
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
 * Classe que faz a conexão com o banco de dados
 *
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */
final class Connection
{
    /**
     * @var Boolean
     * @access private
     */
    private $_transaction = false;
    /**
     * @var PDO
     * @access private
     */
    private $_pdo;

    /**
     * Função que constrói a classe fazendo
     * uma conexão ao PDO
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $opcoes = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_CASE => PDO::CASE_LOWER
        );
        if (DEV) {
            $this->_pdo = new PDO(
                'mysql:host=192.168.0.8;port=3306;dbname=db_new_cms',
                'root', 'my09123', $opcoes
            );

        } else {
            $this->_pdo = new PDO(
                'mysql:host=192.168.0.8;port=3306;dbname=db_new_cms',
                'root', 'my09123', $opcoes
            );
        }
    }


    /**
     * Função que inicia uma transação
     *
     * @return void
     * @access public
     */
    public function inTransaction()
    {
        if (!$this->_transaction) {
            $this->_pdo->beginTransaction();
            $this->_transaction = true;
        }
    }


    /**
     * Função que verifica se existe um start transiction ativa
     *
     * @return boolean
     * @access public
     */
    public function getTransaction()
    {
        return $this->_transaction;
    }


    /**
     * Função que prepara uma query para ser executada
     *
     * @param string $query {query no formato SQL}
     *
     * @return object
     * @access public
     */
    public function prepareQuery($query)
    {
        return $this->_pdo->prepare($query);
    }


    /**
     * Função que commita a conexão
     *
     * @return void
     * @access public
     */
    public function commit()
    {
        $this->_pdo->commit();
        $this->_transaction = false;
    }


    /**
     * Função que retrocede os dados junto a conexão
     *
     * @return void
     * @access public
     */
    public function rollBack()
    {
        $this->_pdo->rollBack();
        $this->_transaction = false;
    }


    /**
     * Função que recupera o último id criado no banco
     *
     * @return Integer
     * @access public
     */
    public function getLastId()
    {
        return $this->_pdo->lastInsertId();
    }

}
?>
