<?php
$actions = Actions::getActions('', '');
if ($actions) {
    foreach ($actions as $action) {
        $name        = $action->getAttribute('id_action');
        $RULE[$name] = Rules::checkRuleUser($action, $USER_LOGGED, '', $typeContent);
        if ($RULE[$name] == '') {
            $RULE[$name] = Rules::checkRuleProfile($action, $USER_LOGGED->getAttribute('profile'), '', $typeContent);
        }
    }
}
?>