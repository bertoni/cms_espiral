<?php
/**
 * Arquivo que traz as funções que serão usadas na aplicação via AJAX
 *
 * PHP Version 5.3
 *
 * @category Functions
 * @package  Tools
 * @name     Functions AJAX
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
*/

require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';
$action = $_POST['action'];

/**
 * Função que grava as permissões de ações para perfis em Conteúdo de Módulos
 *
 * @param String  $rule           {Ação a ser gravada}
 * @param String  $profile        {Perfil a ser gravado}
 * @param String  $content_module {Conteúdo de Módulo a ser gravado}
 * @param String  $type_content   {Tipo de Conteúdo a ser gravado}
 * @param Integer $value          {Valor (0 ou 1) a ser gravado}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'setRuleProfileContentModuleTypeContent') {
    $rule       = new Actions($_POST['rule']);
    $profile    = new Profiles(trim($_POST['profile']));
    $ct_module  = new ContentsModule(trim($_POST['content_module']));
    $tp_content = new TypeContents(trim($_POST['type_content']));
    $value      = (int)$_POST['value'];

    if ($rule->exists() && $profile->exists() && $ct_module->exists()) {
        $ret = Rules::setRuleProfile($rule, $profile, $value, $ct_module);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Regra alterada com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a regra') . '." }';
        }
    } else if ($rule->exists() && $profile->exists() && $tp_content->exists()) {
        $ret = Rules::setRuleProfile($rule, $profile, $value, '', $tp_content);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Regra alterada com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a regra') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a regra') . '." }';
    }
    exit;
}


/**
 * Grava as permissões de ações para usuários em Conteúdo de Módulos
 *
 * @param String  $rule           {Ação a ser gravada}
 * @param String  $user           {Usuário a ser gravado}
 * @param String  $content_module {Conteúdo de Módulo a ser gravado}
 * @param String  $type_content   {Tipo de Conteúdo a ser gravado}
 * @param Integer $value          {Valor (0 ou 1) a ser gravado}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'setRuleUserContentModuleTypeContent') {
    $rule       = new Actions($_POST['rule']);
    $user       = new Users($_POST['user']);
    $ct_module  = new ContentsModule(trim($_POST['content_module']));
    $tp_content = new TypeContents(trim($_POST['type_content']));
    $value      = $_POST['value'];

    if ($rule->exists() && $user->exists() && $ct_module->exists()) {
        $ret = Rules::setRuleUser($rule, $user, $value, $ct_module);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Regra alterada com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a regra') . '." }';
        }
    } else if ($rule->exists() && $user->exists() && $tp_content->exists()) {
        $ret = Rules::setRuleUser($rule, $user, $value, '', $tp_content);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Regra alterada com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a regra') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a regra') . '." }';
    }
    exit;
}


/**
 * Função que relaciona ações com tipos de conteúdos
 *
 * @param String  $rule         {Ação a ser relacionada}
 * @param String  $type_content {Tipo de conteúdo a ser relacionado}
 * @param Integer $value        {Valor (0 ou 1) a ser gravado}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'setRuleActionTypeContent') {
    $rule         = new Actions($_POST['rule']);
    $type_content = new TypeContents($_POST['type_content']);
    $value        = $_POST['value'];

    if ($rule->exists() && $type_content->exists()) {
        $ret = TypeContents::setActionByTypeContent($type_content, $rule, $value);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Relação alterada com sucesso') . '!" }';
        } else {
            echo '{
                "status" : "0",
                "msg" :
                "' . $i18n->_('Não foi possível alterar a relação') . '."
            }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar a relação') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id do Perfil
 *
 * @param String $id {Id do perfil a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeProfileById') {
    $profile = new Profiles(trim($_POST['id']));

    if ($profile->exists()) {
        $ret = Profiles::removeProfile($profile);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Perfil excluído com sucesso') . '!" }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover o perfil') . '."
            }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o perfil') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id a Ação
 *
 * @param String $id {Id da ação a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeActionById') {
    $act = new Actions(trim($_POST['id']));

    if ($act->exists()) {
        $ret = Actions::removeAction($act);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Ação excluída com sucesso') . '!" }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover a ação') . '."
            }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover a ação') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id o Conteúdo de Módulo
 *
 * @param String $id {Id do Conteúdo de módulo a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeContentModuleById') {
    $ct_module = new ContentsModule(trim($_POST['id']));

    if ($ct_module->exists()) {
        $ret = ContentsModule::removeContentModule($ct_module);
        if ($ret) {
            echo '{
                "status" : "1",
                "msg" : "' . $i18n->_('Conteúdo de Módulo excluído com sucesso') . '!"
            }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover o Conteúdo de Módulo') . '."
            }';
        }
    } else {
        echo '{
            "status" : "0",
            "msg" : "' . $i18n->_('Não foi possível remover o Conteúdo de Módulo') . '."
        }';
    }
    exit;
}


/**
 * Função que remove pelo id o Módulo
 *
 * @param String $id {Id do módulo a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeModuleById') {
    $module = new Modules(trim($_POST['id']));

    if ($module->exists()) {
        $ret = Modules::removeModule($module);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Módulo excluído com sucesso') . '!" }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover o Módulo') . '."
            }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Módulo') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id o Usuário
 *
 * @param String $id {Id do Usuário a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeUserById') {
    $user = new Users(trim($_POST['id']));

    if ($user->exists()) {
        $ret = Users::removeUser($user);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Usuário excluído com sucesso') . '!" }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover o Usuário') . '."
            }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Usuário') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id o Filtro
 *
 * @param String $id {Id do Filtro a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeFilterById') {
    $filter = new Filters(trim($_POST['id']));

    if ($filter->exists()) {
        $ret = Filters::removeFilter($filter);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Filtro excluído com sucesso') . '!" }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover o Filtro') . '."
            }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Filtro') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id do Tipo de Conteúdo
 *
 * @param String $id {Id do Tipo de Conteúdo a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeTypeContentById') {
    $type_content = new TypeContents($_POST['id']);

    if ($type_content->exists()) {
        $ret = TypeContents::removeTypeContent($type_content);
        if ($ret) {
            echo '{
                "status" : "1",
                "msg" : "' . $i18n->_('Tipo de conteúdo excluído com sucesso') . '!"
            }';
        } else {
            echo '{
                "status" : "0",
                "msg" : "' . $i18n->_('Não foi possível remover o Tipo de conteúdo') . '."
            }';
        }
    } else {
        echo '{
            "status" : "0",
            "msg" : "' . $i18n->_('Não foi possível remover o Tipo de conteúdo') . '."
        }';
    }
    exit;
}


/**
 * Função que remove pelo id o Campo
 *
 * @param String $id {Id do Campo a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeFormFieldById') {
    $form_field = new FormFields($_POST['id']);

    if ($form_field->exists()) {
        $ret = FormFields::removeFormFiled($form_field);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Campo excluído com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Campo') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Campo') . '." }';
    }
    exit;
}


/**
 * Função que remove pelo id o Conteúdo
 *
 * @param String $id {Id do Conteúdo a ser excluído}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'removeContentById') {
    $content = new Contents((int)$_POST['id']);

    if ($content->exists()) {
        $ret = Contents::removeContent($content);
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Conteúdo excluído com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Conteúdo') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível remover o Conteúdo') . '." }';
    }
    exit;
}
/**
 * Função que define o status do conteúdo como 'e' por id
 *
 * @param String $id {Id do Conteúdo a ser alterado}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'enableContentById') {
    $content = new Contents((int)$_POST['id']);

    if ($content->exists()) {
        $content->setAttribute('status', new Status('a'));
        $ret = $content->save();
        if ($ret) {
            echo '{ "status" : "1", "msg" : "' . $i18n->_('Conteúdo alterado com sucesso') . '!" }';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o Conteúdo') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o Conteúdo') . '." }';
    }
    exit;
}


/**
 * Função que busca os filhos de um filtro
 *
 * @param String $id {Id do Filtro a ser buscado seus filhos}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'getChildsByFilter') {

    $filter = new Filters(isset($_POST['filter']) ? $_POST['filter'] : '');

    if ($filter->exists()) {
        $ret = Filters::getFilters('', '', 'name_filter', new Status('a'), '', $filter);
        if ($ret) {
            $total = count($ret);
            echo '{ "status" : "1", "filters" : [';
            foreach ($ret as $key=>$filter) {
                echo '{ "id" : "' . $filter->getAttribute('id_filter') . '", "name" : "' . $filter->getAttribute('name_filter') . '" }';
                if ($total != ($key+1)) { echo ','; }
            }
            echo ']}';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Este Filtro não possui filhos') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Filtro não existente') . '" }';
    }
    exit;
}



/**
 * Função que busca os filtros por nome
 *
 * @param String $name {Nome do Filtro a ser buscado}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'listFiltersByName') {

    if (isset($_POST['name'])) {
        $par = (isset($_POST['parent']) ? $_POST['parent'] : '');
        if (is_numeric($par)) {
            $par = (int)$par;
        } else if ($par == true) {
            $par = true;
        }
        $ret = Filters::getFilters('', '', 'name_filter', new Status('a'), $_POST['name'], $par);
        if ($ret) {

            function mountArray($parent, $arr = array()) {
                $parent = $parent->getAttribute('filter_parent');
                if ($parent instanceof Filters) {
                    $arr[] = $parent->getAttribute('name_filter');
                    return mountArray($parent, $arr);
                } else {
                    return $arr;
                }
            }

            function getParents($parents) {
                $arr = mountArray($parents);
                krsort($arr);
                $ret = '';
                if (count($arr) > 0) {
                    foreach ($arr as $value) {
                        $ret .= $value . ' > ';
                    }
                }
                return $ret;
            }

            $total = count($ret);
            echo '{ "status" : "1", "filters" : [';
            foreach ($ret as $key=>$filter) {
                echo '{ "id" : "' . $filter->getAttribute('id_filter') . '", "name" : "' . getParents($filter) . $filter->getAttribute('name_filter') . '" }';
                if ($total != ($key+1)) { echo ','; }
            }
            echo ']}';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Este Filtro não possui filhos') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível buscar os filtros') . '." }';
    }
    exit;
}



/**
 * Função que altera a relação entre Tipo de Conteúdos com Tipo de Conteúdos
 *
 * @param Boolean $rule   {Defini se será uma inclusão ou remoção}
 * @param Integer $parent {Tipo de Conteúdo pai}
 * @param Integer $child  {Tipo de Conteúdo filho}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'changeTypeContentsRTypeContents') {

    if ($_POST['rule'] == 'true') {
        $parent = new TypeContents($_POST['parent']);
        $child  = new TypeContents($_POST['child']);
        if ($parent->exists() && $child->exists()) {
            $ret = TypeContents::setRelTypeContentTypeContent($parent, $child, true);
            if ($ret) {
                echo '{ "status" : "1", "msg" : "' . $i18n->_('Relacionamento alterado com sucesso') . '." }';
            } else {
                echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
            }
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
        }
    } else if ($_POST['rule'] == 'false') {
    $parent = new TypeContents($_POST['parent']);
        $child  = new TypeContents($_POST['child']);
        if ($parent->exists() && $child->exists()) {
            $ret = TypeContents::setRelTypeContentTypeContent($parent, $child, false);
            if ($ret) {
                echo '{ "status" : "1", "msg" : "' . $i18n->_('Relacionamento alterado com sucesso') . '." }';
            } else {
                echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
            }
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
    }

}



/**
 * Função que busca os Conteúdos de um Tipo de Conteúdo
 *
 * @param String  $id     {Id do Tipo de Conteúdo a ser buscado}
 * @param Boolean $status {Status do Tipo de Conteúdo a ser buscado}
 * @param Integer $begin  {Início da busca de Conteúdos}
 * @param Integer $end    {Quantidade da busca de Conteúdos}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'getContentsByTypeContent') {

    $typeContent = new TypeContents(isset($_POST['id']) ? $_POST['id'] : '');

    if ($typeContent->exists()) {
        $arr_content_t = Contents::getContents('', '', '', new Status($_POST['status'] ? 'a' : 'w'), $typeContent);
        $arr_content   = Contents::getContents((int)$_POST['begin'], (int)$_POST['end'], 'title', new Status($_POST['status'] ? 'a' : 'w'), $typeContent);
        if ($arr_content) {
            $rest  = count($arr_content_t) - (count($arr_content) + $_POST['begin']);
            $total = count($arr_content);
            echo '{ "status" : "1", "restantes" : "' . $rest . '", "contents" : [';
            foreach ($arr_content as $key=>$content) {
                echo '{';
                    echo '"id" : "' . $content->getAttribute('id_content') . '", ';
                    echo '"title" : "' . $content->getAttribute('title') . '", ';
                    echo '"status" : "' . $content->getAttribute('status')->getAttribute('name_status') . '", ';
                    echo '"id_type" : "' . $content->getAttribute('type_content')->getAttribute('id_type_content') . '", ';
                    echo '"type_content" : "' . $content->getAttribute('type_content')->getAttribute('name_type_content') . '"';
                echo ' }';
                if ($total != ($key+1)) { echo ','; }
            }
            echo ']}';
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não existem mais Conteúdos') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Tipo de Conteúdo inexistente') . '." }';
    }
    exit;
}




/**
 * Função que altera a relação de Conteúdos com Conteúdos
 *
 * @param Boolean $rule   {Defini se será uma inclusão ou remoção}
 * @param Integer $parent {Conteúdo pai}
 * @param Integer $child  {Conteúdo filho}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
if ($action == 'changeContentsRContents') {

    if ($_POST['rule'] == 'true') {
        $parent = new Contents($_POST['parent']);
        $child  = new Contents($_POST['child']);
        if ($parent->exists() && $child->exists()) {
            $ret = Contents::setRelContentsRContents($parent, $child, true);
            if ($ret) {
                echo '{ "status" : "1", "msg" : "' . $i18n->_('Relacionamento alterado com sucesso') . '." }';
            } else {
                echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
            }
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
        }
    } else if ($_POST['rule'] == 'false') {
        $parent = new Contents($_POST['parent']);
        $child  = new Contents($_POST['child']);
        if ($parent->exists() && $child->exists()) {
            $ret = Contents::setRelContentsRContents($parent, $child, false);
            if ($ret) {
                echo '{ "status" : "1", "msg" : "' . $i18n->_('Relacionamento alterado com sucesso') . '." }';
            } else {
                echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
            }
        } else {
            echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
        }
    } else {
        echo '{ "status" : "0", "msg" : "' . $i18n->_('Não foi possível alterar o relacionamento') . '." }';
    }

}
?>
