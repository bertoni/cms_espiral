<?php
/**
 * Arquivo que traz a classe de Regras do CMS
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Configuration
 * @name     Rules
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Regras do CMS
 *
 * @category Classes
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 *
 */
abstract class Rules
{
    /**
     * @var String
     * @access public
     */
    const TABLE_PRO_MOD = 'tbl_rules_content_modules_actions_profiles';
    /**
     * @var String
     * @access public
     */
    const TABLE_PRO_CONT = 'tbl_rules_type_contents_profiles';
    /**
     * @var String
     * @access public
     */
    const TABLE_USE_MOD = 'tbl_rules_content_modules_actions_users';
    /**
     * @var String
     * @access public
     */
    const TABLE_USE_CONT = 'tbl_rules_type_contents_users';

    /**
     * Verifica a permissão do perfil entre Conteúdo de Módulo e Tipo de Conteúdo
     *
     * @param Actions  $action       {Ação a ser verificada}
     * @param Profiles $profile      {Perfil a ser verificada}
     * @param String   $ct_module    {Conteúdo de Módulo a ser verificada}
     * @param String   $type_content {Tipo de Conteúdo a ser verificada}
     *
     * @return boolean
     * @access public
     */
    public static function checkRuleProfile(
        Actions $action,
        Profiles $profile,
        $ct_module = '',
        $type_content = ''
    ) {
        if ($action->exists() && $profile->exists()) {
            $retorno = false;
            if ($ct_module instanceof ContentsModule) {
                $sql = 'SELECT * FROM ' . self::TABLE_PRO_MOD . ' WHERE ' .
                       'fk_id_action = ? AND fk_id_profile = ?
                       AND fk_id_content_module = ?';
                $values = array($action->getAttribute('id_action'),
                                $profile->getAttribute('id_profile'),
                                $ct_module->getAttribute('id_content_module'));
                $REGISTRY   = Registry::getInstance();
                $connection = $REGISTRY->get('dbmysql');
                $cursor     = $connection->prepareQuery($sql);
                $retorno = $cursor->execute($values);
                $retorno = $cursor->rowCount();
            } else if ($type_content instanceof TypeContents) {
                $sql = 'SELECT * FROM ' . self::TABLE_PRO_CONT . ' WHERE ' .
                'fk_id_action = ? AND fk_id_profile = ? AND fk_id_type_content = ?';
                $values = array($action->getAttribute('id_action'),
                                $profile->getAttribute('id_profile'),
                                $type_content->getAttribute('id_type_content'));
                $REGISTRY   = Registry::getInstance();
                $connection = $REGISTRY->get('dbmysql');
                $cursor     = $connection->prepareQuery($sql);
                $retorno = $cursor->execute($values);
                $retorno = $cursor->rowCount();
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Grava a permissão de um perfil entre Conteúdo de Módulo e Tipo de Conteúdo
     *
     * @param Actions  $action       {Ação a ser verificada}
     * @param Profiles $profile      {Perfil a ser verificada}
     * @param Integer  $rule         {Permissão a ser gravada}
     * @param String   $ct_module    {Conteúdo de Módulo a ser verificada}
     * @param String   $type_content {Tipo de Conteúdo a ser verificada}
     *
     * @return boolean
     * @access public
     */
    public static function setRuleProfile(
        Actions $action,
        Profiles $profile,
        $rule,
        $ct_module = '',
        $type_content = ''
    ) {
        if ($action->exists() && $profile->exists()) {
            $retorno = false;
            if ($ct_module instanceof ContentsModule) {
                $current = self::checkRuleProfile($action, $profile, $ct_module);
                if ($rule) {
                    // INSERT
                    if (!$current) {
                        $sql    = 'INSERT INTO ' . self::TABLE_PRO_MOD .
                        ' (fk_id_action, fk_id_profile, fk_id_content_module)' .
                        ' VALUES (?, ?, ?);';
                        $values = array(
                            $action->getAttribute('id_action'),
                            $profile->getAttribute('id_profile'),
                            $ct_module->getAttribute('id_content_module')
                        );

                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    } else {
                        $retorno = true;
                    }
                } else {
                    // DELETE
                    if ($current) {
                        $sql        = 'DELETE FROM ' . self::TABLE_PRO_MOD .
                                      ' WHERE fk_id_action = ?
                                      AND fk_id_profile = ?' .
                                      ' AND fk_id_content_module = ?;';
                        $values     = array(
                            $action->getAttribute('id_action'),
                            $profile->getAttribute('id_profile'),
                            $ct_module->getAttribute('id_content_module')
                        );
                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    } else {
                        $retorno = true;
                    }
                }
            } else if ($type_content instanceof TypeContents) {
                $current = self::checkRuleProfile(
                    $action,
                    $profile,
                    '',
                    $type_content
                );

                if ($rule) {
                    // INSERT
                    if (!$current) {
                        $sql    = 'INSERT INTO ' . self::TABLE_PRO_CONT .
                        ' (fk_id_action, fk_id_profile, fk_id_type_content)' .
                        ' VALUES (?, ?, ?);';
                        $values = array(
                            $action->getAttribute('id_action'),
                            $profile->getAttribute('id_profile'),
                            $type_content->getAttribute('id_type_content')
                        );

                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    } else {
                        $retorno = true;
                    }
                } else {
                    // DELETE
                    if ($current) {
                        $sql        = 'DELETE FROM ' . self::TABLE_PRO_CONT .
                                      ' WHERE fk_id_action = ?
                                        AND fk_id_profile = ?' .
                                      ' AND fk_id_type_content = ?;';
                        $values     = array(
                            $action->getAttribute('id_action'),
                            $profile->getAttribute('id_profile'),
                            $type_content->getAttribute('id_type_content')
                        );
                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    } else {
                        $retorno = true;
                    }
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }


    /**
     * Verifica a permissão para um Conteúdo de Módulo e um Tipo de Conteúdo
     *
     * @param Actions $action       {Ação a ser verificada}
     * @param Users   $user         {Usuário a ser verificada}
     * @param String  $ct_module    {Conteúdo de Módulo a ser verificada}
     * @param String  $type_content {Tipo de Conteúdo a ser verificada}
     *
     * @return Integer or String {'' => Não Existe, 0 => false, 1 => true}
     * @access public
     */
    public static function checkRuleUser(
        Actions $action,
        Users $user,
        $ct_module = '',
        $type_content = ''
    ) {
        if ($action->exists() && $user->exists()) {
            $retorno = '';
            if ($ct_module instanceof ContentsModule) {
                $sql = 'SELECT * FROM ' . self::TABLE_USE_MOD . ' WHERE ' .
                'fk_id_action = ? AND fk_id_user = ? AND fk_id_content_module = ?';
                $values = array($action->getAttribute('id_action'),
                                $user->getAttribute('id_user'),
                                $ct_module->getAttribute('id_content_module'));
                $REGISTRY   = Registry::getInstance();
                $connection = $REGISTRY->get('dbmysql');
                $cursor     = $connection->prepareQuery($sql);
                $cursor->execute($values);
                if ($cursor->rowCount() > 0) {
                    $linha   = $cursor->fetch(PDO::FETCH_ASSOC);
                    $retorno = $linha['status'];
                }
            } else if ($type_content instanceof TypeContents) {
                $sql = 'SELECT * FROM ' . self::TABLE_USE_CONT . ' WHERE ' .
                'fk_id_action = ? AND fk_id_user = ? AND fk_id_type_content = ?';
                $values = array($action->getAttribute('id_action'),
                                $user->getAttribute('id_user'),
                                $type_content->getAttribute('id_type_content'));
                $REGISTRY   = Registry::getInstance();
                $connection = $REGISTRY->get('dbmysql');
                $cursor     = $connection->prepareQuery($sql);
                $cursor->execute($values);
                if ($cursor->rowCount() > 0) {
                    $linha   = $cursor->fetch(PDO::FETCH_ASSOC);
                    $retorno = $linha['status'];
                }
            }
            return $retorno;
        } else {
            return '';
        }
    }


    /**
     * Função que grava a permissão de um usuário
     *
     * @param Actions $action       {Ação a ser verificada}
     * @param Users   $user         {Usuário a ser verificada}
     * @param Integer $rule         {Permissão a ser gravada}
     * @param String  $ct_module    {Conteúdo de Módulo a ser verificada}
     * @param String  $type_content {Tipo de Conteúdo a ser verificada}
     *
     * @return boolean
     * @access public
     */
    public static function setRuleUser(
        Actions $action,
        Users $user,
        $rule,
        $ct_module = '',
        $type_content = ''
    ) {
        if ($action->exists() && $user->exists()) {
            $retorno = false;
            if ($ct_module instanceof ContentsModule) {
                if ($rule == '1' || $rule == '0') {
                    // INSERT
                    $current = self::checkRuleUser($action, $user, $ct_module);
                    if (!$current) {
                        $sql    = 'INSERT INTO ' . self::TABLE_USE_MOD .
                        ' (fk_id_action, fk_id_user, fk_id_content_module, status)'.
                        ' VALUES (?, ?, ?, ?);';
                        $values = array(
                            $action->getAttribute('id_action'),
                            $user->getAttribute('id_user'),
                            $ct_module->getAttribute('id_content_module'),
                            $rule
                        );

                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    } else {
                        $sql    = 'UPDATE ' . self::TABLE_USE_MOD .
                        ' SET status = ? WHERE fk_id_action = ? AND ' .
                        ' fk_id_user = ? AND fk_id_content_module = ?';
                        $values = array(
                            $rule,
                            $action->getAttribute('id_action'),
                            $user->getAttribute('id_user'),
                            $ct_module->getAttribute('id_content_module')
                        );

                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    }
                } else {
                    // DELETE
                    $sql        = 'DELETE FROM ' . self::TABLE_USE_MOD .
                                  ' WHERE fk_id_action = ? AND fk_id_user = ?' .
                                  ' AND fk_id_content_module = ?;';
                    $values     = array(
                        $action->getAttribute('id_action'),
                        $user->getAttribute('id_user'),
                        $ct_module->getAttribute('id_content_module')
                    );

                    $REGISTRY   = Registry::getInstance();
                    $connection = $REGISTRY->get('dbmysql');
                    $cursor     = $connection->prepareQuery($sql);
                    $retorno    = $cursor->execute($values);
                }
            } else if ($type_content instanceof TypeContents) {
                if ($rule == '1' || $rule == '0') {
                    // INSERT
                    $current = self::checkRuleUser(
                        $action,
                        $user,
                        '',
                        $type_content
                    );

                    if (!$current) {
                        $sql    = 'INSERT INTO ' . self::TABLE_USE_CONT .
                        ' (fk_id_action, fk_id_user, fk_id_type_content, status)' .
                        ' VALUES (?, ?, ?, ?);';
                        $values = array(
                            $action->getAttribute('id_action'),
                            $user->getAttribute('id_user'),
                            $type_content->getAttribute('id_type_content'),
                            $rule
                        );

                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    } else {
                        $sql    = 'UPDATE ' . self::TABLE_USE_CONT .
                        ' SET status = ? WHERE fk_id_action = ? AND ' .
                        ' fk_id_user = ? AND fk_id_type_content = ?';
                        $values = array(
                            $rule,
                            $action->getAttribute('id_action'),
                            $user->getAttribute('id_user'),
                            $type_content->getAttribute('id_type_content')
                        );

                        $REGISTRY   = Registry::getInstance();
                        $connection = $REGISTRY->get('dbmysql');
                        $cursor     = $connection->prepareQuery($sql);
                        $retorno    = $cursor->execute($values);
                    }
                } else {
                    // DELETE
                    $sql        = 'DELETE FROM ' . self::TABLE_USE_CONT .
                                  ' WHERE fk_id_action = ? AND fk_id_user = ?' .
                                  ' AND fk_id_type_content = ?;';
                    $values     = array(
                        $action->getAttribute('id_action'),
                        $user->getAttribute('id_user'),
                        $type_content->getAttribute('id_type_content')
                    );
                    
                    $REGISTRY   = Registry::getInstance();
                    $connection = $REGISTRY->get('dbmysql');
                    $cursor     = $connection->prepareQuery($sql);
                    $retorno    = $cursor->execute($values);
                }
            }
            return $retorno;
        } else {
            return false;
        }
    }
}
?>