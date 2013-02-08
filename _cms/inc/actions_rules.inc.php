<?php
/**
 * Recupera a informação das ações
 *
 * PHP Version 5.3
 *
 * @category Page
 * @package  CMS
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

$actions = Actions::getActions('', '');
if ($actions) {
    foreach ($actions as $action) {
        $name        = $action->getAttribute('id_action');
        $RULE[$name] = Rules::checkRuleUser($action, $USER_LOGGED, $contenModule);
        if ($RULE[$name] == '') {
            $RULE[$name] = Rules::checkRuleProfile($action, $USER_LOGGED->getAttribute('profile'), $contenModule);
        }
    }
}
?>