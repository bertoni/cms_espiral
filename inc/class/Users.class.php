<?php
/**
 * Arquivo que traz a classe de Usuários
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Users
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Usuários
 *
 * @category Classes
 * @package  Users
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */
class Users
{
    /**
     * @var Integer
     * @access protected
     */
    protected $id_user;
    /**
     * @var Profiles
     * @access protected
     */
    protected $profile;
    /**
     * @var Status
     * @access protected
     */
    protected $status;
    /**
     * @var String
     * @access protected
     */
    protected $name;
    /**
     * @var String
     * @access protected
     */
    protected $email;
    /**
     * @var String
     * @access protected
     */
    protected $pass;
    /**
     * @var Boolean
     * @access protected
     */
    protected $change_pass;
    /**
     * @var Integer
     * @access protected
     */
    protected $date_entry;
    /**
     * @var Array
     * @access protected
     */
    protected $date_last_login = array();
    /**
     * @var Integer
     * @access protected
     */
    protected $num_logins;
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
    const TABLE = 'tbl_users';
    /**
     * @var String
     * @access public
     */
    const TABLE_LOGINS = 'tbl_users_logins';

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
        case         'id_user':
        case         'profile':
        case          'status':
        case            'name':
        case           'email':
        case            'pass':
        case     'change_pass':
        case      'date_entry':
        case 'date_last_login':
        case      'num_logins':
        case             'log':
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
        case 'id_user':
            $this->$attribute = (int)$value;
            break;
        case 'change_pass':
            $this->$attribute = (boolean)$value;
            break;
        case  'name':
        case 'email':
        case  'pass':
            $this->$attribute = (string)$value;
            break;
        case 'profile':
            if ($value instanceof Profiles && $value->exists()) {
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
     * @param String $id_user {Id do Usuário a ser criado}
     *
     * @return void
     * @access public
     */
    public function __construct($id_user)
    {
        $this->setAttribute('id_user', $id_user);
        $this->_load();
    }


    /**
     * Função que busca um usuário junto ao Banco de Dados
     *
     * @return void
     * @access private
     */
    private function _load()
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT * FROM ' . self::TABLE . ' WHERE pk_id_user = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array($this->getAttribute('id_user')));
        if ($cursor->rowCount() > 0) {
            $linha   = $cursor->fetch(PDO::FETCH_ASSOC);
            $status  = new Status($linha['fk_id_status']);
            $profile = new Profiles($linha['fk_id_profile']);
            if ($status->exists() && $profile->exists()) {
                $this->setAttribute('id_user', $linha['pk_id_user']);
                $this->setAttribute('profile', $profile);
                $this->setAttribute('status', $status);
                $this->setAttribute('name', $linha['name']);
                $this->setAttribute('email', $linha['email']);
                $this->setAttribute('pass', $linha['pass']);
                $this->setAttribute('change_pass', $linha['change_pass']);
                $this->date_entry = $linha['date_entry'];
                
                $sql    = 'SELECT date_entry FROM ' . self::TABLE_LOGINS .
                ' WHERE fk_id_user = ? ORDER BY date_entry DESC;';
                $cursor = $connection->prepareQuery($sql);
                $cursor->execute(array($this->getAttribute('id_user')));
                if ($cursor->rowCount() > 0) {
                    while ($logins = $cursor->fetch(PDO::FETCH_ASSOC)) {
                        $this->date_last_login[] = $logins['date_entry'];
                    }
                    $this->num_logins = count($this->date_last_login);
                } else {
                    $this->num_logins = 0;
                }
                
                $this->setAttribute('log', $linha['log']);
                $this->_authentic = true;
            }
        }
    }


    /**
     * Função que salva o Objeto na base de dados
     *
     * @return Boolean
     * @access public
     */
    public function save()
    {
        $return = false;
        if (!$this->exists()) {
            // INSERT
            $sql    = 'INSERT INTO ' . self::TABLE . ' (' .
            'fk_id_profile, fk_id_status, change_pass,' .
            'date_entry, name, email,' .
            'pass, log ) VALUES (?, ?, 0,' .
            mktime() . ', ?, ?, ?, ?);';
            $values = array(
                          $this->getAttribute('profile')->getAttribute('id_profile'),
                          $this->getAttribute('status')->getAttribute('id_status'),
                          $this->getAttribute('name'),
                          $this->getAttribute('email'),
                          sha1(SALT . 'alterar'),
                          $this->getAttribute('log')
                      );

            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $ret        = $cursor->execute($values);
            if ($ret) {
                $this->setAttribute('id_user', $connection->getLastId());
                $return = true;
            }
        } else {
            // UPDATE
            $sql    = 'UPDATE ' . self::TABLE . ' SET
                       fk_id_profile = ?,
                       fk_id_status  = ?,
                       name          = ?,
                       email         = ?,
                       log           = ?
                       WHERE
                       pk_id_user = ?;';
            $values = array(
                    $this->getAttribute('profile')->getAttribute('id_profile'),
                    $this->getAttribute('status')->getAttribute('id_status'),
                    $this->getAttribute('name'),
                    $this->getAttribute('email'),
                    $this->getAttribute('log'),
                    $this->getAttribute('id_user')
            );
            
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $return     = $cursor->execute($values);
        }
        return $return;
    }


    /**
     * Função que altera a senha do usuário
     *
     * @param String $new_pass {Nova senha a ser usada}
     *
     * @return boolean
     * @access public
     */
    public function changePass($new_pass)
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'UPDATE ' . self::TABLE . ' SET
                       pass        = ?,
                       change_pass = 0,
                       log         = CONCAT(log, " Senha Alterada em ' .
                       strftime('%d/%m/%Y %H:%M:%S', mktime()) .
                       '" ' . "\n" . ')
                       WHERE
                       pk_id_user = ?;';
            $info = array(
                    sha1(SALT . trim($new_pass)),
                    $this->getAttribute('id_user')
            );
            $cursor = $connection->prepareQuery($sql);
            return $cursor->execute($info);
    }


    /**
     * Função que efetua o login para o CMS
     *
     * @param String $email {E-mail do usuário a se logar}
     * @param String $pass  {Senha do usuário a se logar}
     *
     * @return boolean
     * @access public
     */
    public static function loginCms($email, $pass)
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT pk_id_user FROM ' . self::TABLE .
        ' WHERE email = ? AND pass = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array(trim($email), sha1(SALT . trim($pass))));
        if ($cursor->rowCount() > 0) {
            $linha = $cursor->fetch(PDO::FETCH_ASSOC);
            $user  = new Users($linha['pk_id_user']);
            if ($user->exists()) {
                if ($user->getAttribute('status')->getAttribute('id_status')=='a') {
                    // Incremento o número de logins
                    $sql    = 'INSERT INTO ' . self::TABLE_LOGINS . ' (fk_id_user, date_entry)
                              VALUES (?, ' . mktime() . ');';
                    $info   = array($user->getAttribute('id_user'));
                    $cursor = $connection->prepareQuery($sql);
                    $cursor->execute($info);


                    $_SESSION['login_cms'] = true;
                    $_SESSION['msg_cms']   = $i18n->_('Bem Vindo') . ' ' .
                                             $user->getAttribute('name') . '.';
                    $_SESSION['user_cms']  = serialize($user);
                    return true;
                } else {
                    $_SESSION['login_cms'] = false;
                    $_SESSION['msg_cms']   = $i18n->_('Você não tem permissão de acesso') . '.';
                    return false;
                }
            } else {
                $_SESSION['login_cms'] = false;
                $_SESSION['msg_cms']   = $i18n->_('Usuário não Encontrado') . '.';
                return false;
            }
        } else {
            $_SESSION['msg_cms'] = $i18n->_('Usuário ou senha inválidos') . '.';
            return false;
        }
    }


    /**
     * Função que verifica a existência de um e-mail de usuário
     *
     * @param String $email {E-mail do usuário a se logar}
     *
     * @return User or Boolean
     * @access public
     */
    public static function checkEmail($email)
    {
        $REGISTRY   = Registry::getInstance();
        $connection = $REGISTRY->get('dbmysql');
        $sql        = 'SELECT pk_id_user FROM ' . self::TABLE .
        ' WHERE email = ?;';
        $cursor     = $connection->prepareQuery($sql);
        $cursor->execute(array(trim($email)));
        if ($cursor->rowCount() > 0) {
            $linha = $cursor->fetch(PDO::FETCH_ASSOC);
            $user  = new Users($linha['pk_id_user']);
            if ($user->exists()) {
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Função que busca usuários
     *
     * @param Integer $begin   {Início de onde deve buscar}
     * @param Integer $end     {Quantidade que deve buscar}
     * @param Integer $order   {Ordenação dos resultados}
     * @param String  $status  {Status dos conteúdos dos módulos}
     * @param String  $profile {Perfil dos usuários}
     * @param String  $name    {Nome do usuário a buscar}
     *
     * @return Users
     * @access private
     */
    public static function getUsers(
        $begin,
        $end,
        $order = '',
        $status = '',
        $profile = '',
        $name = ''
    ) {
        $sql    = 'SELECT pk_id_user FROM ' . self::TABLE . ' WHERE 1=1';
        $values = array();
        if ($status instanceof Status) {
            $sql     .= ' AND fk_id_status = ?';
            $values[] = $status->getAttribute('id_status');
        }
        if ($profile instanceof Profiles) {
            $sql     .= ' AND fk_id_profile = ?';
            $values[] = $profile->getAttribute('id_profile');
        }
        if (!empty($name)) {
            $sql     .= ' AND name LIKE ?';
            $values[] = $name . '%';
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
                $user = new Users($linha['pk_id_user']);
                if ($user->exists()) {
                    $retorno[] = $user;
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Função que exclui um Usuário junto ao Banco de Dados
     *
     * @param Users $user {usuário a ser excluído}
     *
     * @return boolean
     * @access private
     */
    public static function removeUser(Users $user)
    {
        $retorno = 0;
        if ($user->exists()) {
            $sql        = 'DELETE FROM ' . self::TABLE . ' WHERE pk_id_user = ?;';
            $values     = array($user->getAttribute('id_user'));
            $REGISTRY   = Registry::getInstance();
            $connection = $REGISTRY->get('dbmysql');
            $cursor     = $connection->prepareQuery($sql);
            $retorno    = $cursor->execute($values);
        }
        return $retorno;
    }


}
?>