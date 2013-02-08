<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="none" />
        <meta name="googlebot" content="noarchive" />

		<title><?= SYSTEM_NAME ?></title>

		<!-- HTML5 SHIV -->
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

        <!-- TEMA CSS INCLUDE -->
        <?php

        if (isset($_COOKIE['preferences_flag']) && !isset($tema) ) {
            if ($_COOKIE['preferences_flag'] != 'default') {
                echo '<link type="text/css" href="' . PATH_CMS_URL . '/css/temas/' . $_COOKIE['preferences_flag'] . '" media="screen" rel="stylesheet" />';
                $tema = $_COOKIE['preferences_flag'];
            } else {
                echo '<link type="text/css" href="'.PATH_CMS_URL.'/css/temas/default.css" media="screen" rel="stylesheet" />';
            }
        } else if (isset($tema) && $tema!='') {
            echo '<link type="text/css" href="'.PATH_CMS_URL.'/css/temas/'.$tema.'" media="screen" rel="stylesheet" />';
        } else {
            $tema = 'default.css';
            echo '<link type="text/css" href="'.PATH_CMS_URL.'/css/temas/'.$tema.'" media="screen" rel="stylesheet" />';
        }


        ?>

		<!-- CSS INCLUDES -->
        <?php
        if (isset($CSS) && is_array($CSS) && count($CSS) > 0) {
            foreach ($CSS as $value) {
                echo '<link type="text/css" href="' . PATH_CMS_URL . $value . '" media="screen" rel="stylesheet" />';
            }
        }
        ?>

        <!-- JS INCLUDES -->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <?php
        if (isset($JS) && is_array($JS) && count($JS) > 0) {
            foreach ($JS as $value) {
                echo '<script type="text/javascript" src="' . PATH_CMS_URL . $value . '"></script>';
            }
        }
        ?>
        <script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/lib.js"></script>
	</head>
	<body>

		<header>
			<nav class="menu">
                <ul class="clearfix">
                    <li><a href="<?php echo PATH_CMS_URL; ?>/dashboard/" class="nav-home <?php echo $module_code == '' ? 'selected' : ''; ?>"><?php echo $i18n->_('Home'); ?></a></li>
                    <?php
                    $modules = Modules::getModules('', '', '', new Status('a'));
                    if ($modules) {
                        foreach ($modules as $module) {
                            $contentsModules = ContentsModule::getContentsModules('', '', '', new Status('a'), $module, '', '', 1);
                            if ($contentsModules) {
                                $action = new Actions('lis');
                                if ($action->exists()) {
                                    $rule = 0;
                                    foreach ($contentsModules as $contentModule) {
                                        $rule += Rules::checkRuleUser($action, $USER_LOGGED, $contentModule);
        			                    if ($rule == '') {
        			                        $rule += Rules::checkRuleProfile($action, $USER_LOGGED->getAttribute('profile'), $contentModule);
        			                    }
        			                    if ($rule) {
        			                        break;
        			                    }
                                    }
                                    if ($rule && is_dir(PATH_CMS_DOC_ROOT . $module->getAttribute('url'))) {
                                        echo '<li><a href="' . PATH_CMS_URL . $module->getAttribute('url') . '"' . ($module_code == $module->getAttribute('id_module') ? ' class="selected"' : '') . '>' . $module->getAttribute('name_module') . '</a></li>';
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    <li class="current_user"><a href="<?php echo PATH_CMS_URL; ?>/users/users/preferences" <?php echo $module_code == 'users-preferences' ? 'class="selected"' : '' ?>><?php echo $i18n->_('Olá'); ?>, <?php echo $USER_LOGGED->getAttribute('name'); ?></a></li>
                </ul>
			</nav><!-- .menu -->

			<nav class="submenu">
    			<?php
    			if (!empty($module_code)) {
    			    $module = new Modules($module_code);
    			    if ($module->exists()) {
    			        echo '<ul class="clearfix nav-config open">';
    			        $action = new Actions('lis');
    			        $contentsModules = ContentsModule::getContentsModules('', '', '', new Status('a'), $module, '', '', 1);
    			        if ($contentsModules) {
    			            if ($action->exists()) {
        			            foreach ($contentsModules as $contentModule) {
        			                if (is_dir(PATH_CMS_DOC_ROOT . $module->getAttribute('url') . $contentModule->getAttribute('url'))) {
        			                    $rule = Rules::checkRuleUser($action, $USER_LOGGED, $contentModule);
        			                    if ($rule == '') {
        			                        $rule = Rules::checkRuleProfile($action, $USER_LOGGED->getAttribute('profile'), $contentModule);
        			                    }
        			                    if ($rule) {
        			                        echo '<li><a href="' . PATH_CMS_URL . $contentModule->getAttribute('module')->getAttribute('url') . $contentModule->getAttribute('url') . '"' . ($page_code == $contentModule->getAttribute('id_content_module') ? ' class="selected"' : '') . '>' . $contentModule->getAttribute('name_content_module') . '</a></li>';
        			                    }
        			                }
        			            }
    			            }
    			        }
    			        if ($module->getAttribute('id_module') == 'cont') {
    			            $arr_type_contents = TypeContents::getTypeContents('', '', 'name_type_content', new Status('a'));

                            if ($arr_type_contents) {
    			                if ($action->exists()) {
        			                foreach ($arr_type_contents as $type_content) {
        			                    if (is_dir(PATH_CMS_DOC_ROOT . $module->getAttribute('url') . 'contents/')) {
        			                        $rule = Rules::checkRuleUser($action, $USER_LOGGED, '', $type_content);
            			                    if ($rule == '') {
            			                        $rule = Rules::checkRuleProfile($action, $USER_LOGGED->getAttribute('profile'), '', $type_content);
            			                    }
            			                    if ($rule) {
            			                        echo '<li><a href="' . PATH_CMS_URL . $module->getAttribute('url') . 'contents/?cont=' . $type_content->getAttribute('id_type_content') . '"' . ($page_code == $type_content->getAttribute('id_type_content') ? ' class="selected"' : '') . '>' . $type_content->getAttribute('name_type_content') . '</a></li>';
            			                    }
        			                    }
        			                }
    			                }
    			            }
    			        }
    			         echo '</ul>';
    			    }
    			} else {
    			    echo '<ul class="clearfix nav-config open">';
    			        echo '<li><a href="' . PATH_CMS_URL . '/dashboard/approvals/"' . ($page_code == 'approvals' ? ' class="selected"' : '') . '>' . $i18n->_('Aprovações') . '</a></li>';
    			    echo '</ul>';
    			}
    			?>

                <ol class="clearfix actions">
					<li class="float-right"><a href="<?= PATH_CMS_URL ?>/?logout" class="nav-logout"><?php echo $i18n->_('Sair'); ?></a></li>
				</ol>
			</nav><!-- .submenu -->
		</header>

		<div class="wrapper">
			<div class="content <?php echo  $page_code; ?>">
			<div id ="msg-geral" style="display: none;">
				<?php
				if (isset($_GET['alert'])) {
				    echo '<script type="text/javascript">setaMsgGeral("alert", "' . $_GET['alert'] . '");</script>';
				} else if (isset($_GET['ok'])) {
				    echo '<script type="text/javascript">setaMsgGeral("ok", "' . $_GET['ok'] . '");</script>';
				} else if (isset($_GET['erro'])) {
				    echo '<script type="text/javascript">setaMsgGeral("erro", "' . $_GET['erro'] . '");</script>';
				} else if (isset($_GET['info'])) {
				    echo '<script type="text/javascript">setaMsgGeral("info", "' . $_GET['info'] . '");</script>';
				}
				?>
			</div>
