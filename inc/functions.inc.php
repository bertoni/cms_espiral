<?php
/**
 * Arquivo que traz as principais funções que serão usadas na aplicação
 *
 * PHP Version 5.3
 *
 * @category Functions
 * @package  Tools
 * @name     Functions
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
*/

/**
 * Função mágica que carrega as classes requisitadas
 *
 * @param String $class {Função a ser carregada}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  void
 *
 */
function __autoload($class)
{
    try {
        include_once $class . '.class.php';
    } catch (Exception $e) {
        echo $e->getMessage();exit;
    }
}


/**
 * Função que retira acentos de uma palavra
 *
 * @param String $term {Palavra a ser limpada}
 *
 * @package Functions
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  String
 *
 */
function clearEmphasis($term)
{
	$palavras = array(
	'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','Á'=>'A','À'=>'A','Ã'=>'A','Â'=>'A',
	'é'=>'e','è'=>'e','ẽ'=>'e','ê'=>'e','É'=>'E','È'=>'E','Ẽ'=>'E','Ê'=>'E',
	'í'=>'i','ì'=>'i','ĩ'=>'i','î'=>'i','Í'=>'I','Ì'=>'I','Ĩ'=>'I','Î'=>'I',
	'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','Ó'=>'O','Ò'=>'O','Õ'=>'O','Ô'=>'O',
	'ú'=>'u','ù'=>'u','ũ'=>'u','û'=>'u','Ú'=>'U','Ù'=>'U','Ũ'=>'U','Û'=>'U',
	'ç'=>'c','Ç'=>'C','ñ'=>'n','Ñ'=>'N','@'=>'', '#'=>'', '?'=>'', '!'=>'',
	'$'=>'', '%'=>'', '¨'=>'', '&'=>'', '*'=>'', '+'=>'', '_'=>'', '<'=>'','>'=>'',
	';'=>'', ','=>'', '/'=>'','\\'=>'', '^'=>'', '~'=>'', '['=>'',']'=>'',
	'\''=>'','"'=>'', '¹'=>'', '²'=>'', '³'=>'', '£'=>'', '¢'=>'', '¬'=>'','='=>'',
	':'=>''
	);
	foreach($palavras as $key=>$value){
		$term = str_replace($key,$value,$term);
	}
	return utf8_encode($term);
}


/**
 * Função não tão mágica que gera uma senha segura
 *
 * @param int     $chars   {Número de caracteres da senha}
 * @param boolean $number  {Devemos usar números?}
 * @param boolean $special {Devemos usar caracteres especiais?}
 * @param boolean $case    {Devemos usar maiúsculas?}
 *
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  string
 *
 */
function makeSafePassword($chars=10, $number=true, $special=true, $case=true)
{
    $arr_letters=str_split('abcdefghijklmnoprqstuvxz');
    $arr_special=str_split('!@#$%_');
    $password = '';

    for ($i=0;$i<$chars;$i++) {
        if ($i%5==4 && $special) {
            $password .= $arr_special[rand(0, (sizeof($arr_special)-1))];
        } else if ($i%4==3 && $number) {
            $password .= rand(0, 9);
        } else {
            $char = $arr_letters[rand(0, (sizeof($arr_letters)-1))];
            $password .= ( rand(0, 1) == 0 || !$case ? $char : strtoupper($char));
        }
    }

    $password = str_split($password);
    shuffle($password);

    return implode($password);
}


/**
 * Cria cookies para salvar a sessão
 *
 * @param String $user {Usuário}
 * @param String $pass {Senha}
 *
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  null
 */
function setSessionCookies($user, $pass)
{
    // A data de expiração é 30 dias da criação do cookies
    setcookie("user", $user, time()+60*60*24*30);
    setcookie("pass", $pass, time()+60*60*24*30);
                                  //s  m  h  d
}


/**
 * Limpa os cookies de sessã o
 *
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  null
 */
function clearSessionCookies()
{
    setcookie("user", 0, time()-3600 * 25);
    setcookie("pass", 0, time()-3600 * 25);
}


/**
 * Cria cookies para salvar as preferencias
 *
 * @param String $flag {Flag com as preferências}
 *
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  null
 */
function setPreferencesCookies($flag)
{
    setcookie("preferences_flag", $flag, time()+60*60*24*30,"/_cms/");
}


/**
 * Limpa os cookies de preferencias
 *
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  null
 */
function clearPreferencesCookies()
{
    setcookie("preferences_flag", 0, time()-3600 * 25,"/_cms/");
}


/**
 * Prepara os headers e envia um e-mail
 *
 * @param String $destinatario {E-mail de destino}
 * @param String $assunto      {Assunto do e-mail}
 * @param String $corpo        {Mensagem em HTML}
 * @param String $remetente    {E-mail do remetente}
 *
 * @author  Espiral Interativa <ti@espiralinterativa.com>
 * @return  boolean
 */
function sendMail($destinatario, $assunto, $corpo, $remetente='no-reply@espir.al')
{
    $headers  = "MIME-Version: 1.1\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
    $headers .= "From: ".$remetente."\n";
    $headers .= "Return-Path: ".$remetente."\n";

    if (mail($destinatario, $assunto, $corpo, $headers, "-r".$remetente)) {
        return true;
    }
    return false;
}


/**
 * Função que gera o log de todo sistema
 *
 * @param Users  $user {Usuário que está logado}
 * @param String $log  {Log que será usado}
 *
 * @author Espiral Interativa <ti@espiralinterativa.com>
 * @return String
 */
function formatLog(Users $user, $log)
{
    $retorno = '';
    if ($log != '' && $user->exists()) {
        $retorno = strftime('%d/%m/%Y-%H:%M:%S', mktime()) .
        ' - ' . $user->getAttribute('name') . "\n" . $log;
    }
    return $retorno;
}


function generationHtmlSelect(Filters $filter, $html, $class, $requi, $name, $id) {
    if ($filter->getAttribute('filter_parent') instanceof Filters) {
        $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter->getAttribute('filter_parent'));
        if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
            $newHtml  = '';
            $newHtml .= '<select class="filter clear ' . (!empty($class) ? $class : '') . ($requi ? ' required' : '') .
                        '" name="' . $name . '[]" id="' . $id . '">' . "\n";
            foreach ($arrTerms as $term) {
                $selected = ($filter->getAttribute('id_filter') == $term->getAttribute('id_filter') ? ' selected="selected"' : '');
                $newHtml .= '<option value="' . $term->getAttribute('id_filter') . '"' . $selected .
                            '>' . $term->getAttribute('name_filter') . '</option>' . "\n";
            }
            $newHtml .= '</select>' . "\n";
            $html[]   = $newHtml;
            return generationHtmlSelect($filter->getAttribute('filter_parent'), $html, $class, $requi, $name, $id);
        }
    } else {
        return $html;
    }
}


/**
 * Função que monta o html de campos do cms
 *
 * @param ConfigForm $configForm {Configuração do campo a ser gerado}
 *
 * @author Espiral Interativa <ti@espiralinterativa.com>
 * @return String
 */
function FieldHtmlGenerator(ConfigForm $configForm, $value = '')
{
    $html  = '';
    $requi = ($configForm->getAttribute('required') ? true : false);
    $html .= '<br/><br/>' . "\n";
    $name  = $id = $configForm->getAttribute('name');
    $html .= '<label for="' .$name . '">' .
             $configForm->getAttribute('label') . '</label>' .
             ($requi ? '*' : '') . '<br/>' . "\n";
    $class = '';
    if ($configForm->getAttribute('form')->getAttribute('class_css') != '') {
        $class = explode('|', $configForm->getAttribute('form')->getAttribute('class_css'));
        $class = implode(' ', $class);
    }
    switch ($configForm->getAttribute('form')->getAttribute('type_html')) {
        case 'input[text]':
            // ######################################################### input[text]
            $html .= '<input type="text" class="text big w100' . (!empty($class) ? ' ' . $class : '') . ($requi ? ' required' : '') .
                     '" name="' . $name . '" id="' . $id .
                     '"' . ($configForm->getAttribute('max_lenght') != '' ? ' maxlength="' . $configForm->getAttribute('max_lenght') .
                     '"' : '') . ' value="' . $value . '" />' . "\n";
            return $html;
            break;
        case 'input[checkbox]':
            // ######################################################### input[checkbox]
            $value  = (is_array($value) ? $value : explode('||', $value));
            $filter = $configForm->getAttribute('form')->getAttribute('filter');
            if ($filter instanceof Filters && $filter->exists()) {
                $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter);
                if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
                    foreach ($arrTerms as $term) {
                        $checked = (in_array($term->getAttribute('id_filter'), $value) ? ' checked="checked"' : '');
                        $html   .= '<label><input type="checkbox" class="checkbox' . (!empty($class) ? ' ' . $class : '') .
                                   ($requi ? ' required' : '') . '" name="' .
                                   $name . '[]" id="' . $id . '"' . $checked .
                                   ' value="' . $term->getAttribute('id_filter') . '">' . $term->getAttribute('name_filter') .
                                   '</label>' . "\n";
                    }
                }
            } else {
                $arr_values = explode('|', $configForm->getAttribute('form')->getAttribute('value_default'));
                foreach ($arr_values as $values) {
                    $values  = explode(',', $values);
                    $checked = ($values[0] == $value ? ' checked="checked"' : '');
                    $html   .= '<label><input type="checkbox" class="checkbox' . (!empty($class) ? ' ' . $class : '') .
                               ($requi ? ' required' : '') . '" name="' .
                               $name . '[]" id="' . $id . '"' . $checked .
                               ' value="' . $values[0] . '">' . $values[1] . '</label>' . "\n";
                }
            }
            return $html;
            break;
        case 'input[radio]':
            // ######################################################### input[radio]
            $filter = $configForm->getAttribute('form')->getAttribute('filter');
            if ($filter instanceof Filters && $filter->exists()) {
                $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter);
                if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
                    foreach ($arrTerms as $term) {
                        $checked = ($value == $term->getAttribute('id_filter') ? ' checked="checked"' : '');
                        $html   .= '<label><input type="radio" class="radio' . (!empty($class) ? ' ' . $class : '') .
                                   ($requi ? ' required' : '') . '" name="' .
                                   $name . '" id="' . $id . '"' . $checked .
                                   ' value="' . $term->getAttribute('id_filter') . '">' . $term->getAttribute('name_filter') .
                                   '</label>' . "\n";
                    }
                }
            } else {
                $arr_values = explode('|', $configForm->getAttribute('form')->getAttribute('value_default'));
                foreach ($arr_values as $values) {
                    $values  = explode(',', $values);
                    $checked = ($values[0] == $value ? ' checked="checked"' : '');
                    $html   .= '<label><input type="radio" class="radio' . (!empty($class) ? ' ' . $class : '') .
                               ($requi ? ' required' : '') . '" name="' .
                               $name . '" id="' . $id . '"' . $checked .
                               ' value="' . $values[0] . '">' . $values[1] . '</label>' . "\n";
                }
            }
            return $html;
            break;
        case 'input[hidden]':
            // ######################################################### input[hidden]
            $html .= '<input type="hidden"' . (!empty($class) ? ' class="' . $class . '"' : '') . ' name="' . $name . '" id="' . $id . '" value="' . $value . '" />' . "\n";
            return $html;
            break;
        case 'input[password]':
            // ######################################################### input[password]
            $html .= '<input type="password" class="text big w100' . (!empty($class) ? ' ' . $class : '') .
                     ($requi ? ' required' : '') . '" name="' . $name . '" id="' . $id .
                     '"' . ($configForm->getAttribute('max_lenght') != '' ? ' maxlength="' . $configForm->getAttribute('max_lenght') .
                     '"' : '') . ' value="' . $value . '" />' . "\n";
            return $html;
            break;
        case 'input[file]':
            // ######################################################### input[file]
            $html .= '<div class="file-input"><input type="text" class="file text w100' . (!empty($class) ? ' ' . $class : '') . '" name="' . $name . '" id="' . $id . '" value="' . $value . '" /><span class="show-file-browser"></span></div>' . "\n";
            return $html;
            break;
        case 'select':
            // ######################################################### select
            $filter = $configForm->getAttribute('form')->getAttribute('filter');
            if ($filter instanceof Filters && $filter->exists()) {
                $childs = 0;
                if ($value == '') {
                    $html .= '<select class="filter clear ' . (!empty($class) ? $class : '') . ($requi ? ' required' : '') .
                    '" name="' . $name . '" id="' . $id . '">' . "\n";
                    $html .= '<option value="">Escolha os(as) ' . $configForm->getAttribute('form')->getAttribute('name_form_field') . '</option>' . "\n";

                    $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter);
                    if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
                        foreach ($arrTerms as $term) {
                            $arrchilds = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $term);
                            if ($arrchilds) {
                                $childs++;
                            }
                            $selected = ($value == $term->getAttribute('id_filter') ? ' selected="selected"' : '');
                            $html    .= '<option value="' . $term->getAttribute('id_filter') .
                                        '"' . $selected . '>' . $term->getAttribute('name_filter') . '</option>' . "\n";
                        }
                    }
                    $html .= '</select>' . "\n";
                } else {

                    $filter_value = new Filters($value);
                    $arr_html     = generationHtmlSelect($filter_value, array(), $class, $requi, $name, $id);
                    if (count($arr_html) > 1) {
                        $childs = 1;
                    }
                    krsort($arr_html);
                    foreach ($arr_html as $val) {
                        $html .= $val;
                    }

                }
            } else {
                $arr_values = explode('|', $configForm->getAttribute('form')->getAttribute('value_default'));
                $html      .= '<select class="' . (!empty($class) ? $class : '') . ($requi ? ' required' : '') .
                              '" name="' . $name . '" id="' . $id . '">' . "\n";
                $html      .= '<option value="">Escolha</option>' . "\n";
                foreach ($arr_values as $values) {
                    $values  = explode(',', $values);
                    $checked = ($values[0] == $value ? ' selected="selected"' : '');
                    $html   .= '<option value="' . $values[0] .
                               '"' . $checked . '>' . $values[1] . '</option>' . "\n";
                }
                $html .= '</select>' . "\n";
            }
            return $html;
            break;
        case 'textarea':
            // ######################################################### textarea
            $html  = '';
            $html .= '<br/><br/>' . "\n";
            $html .= '<label for="' . $name . '">' . $configForm->getAttribute('label') .
                     '</label>' . ($requi ? '*' : '') .
                     ($configForm->getAttribute('max_lenght') != '' ?
                     ' (<span id="limit_' . $configForm->getAttribute('name') .
                     '">' . $configForm->getAttribute('max_lenght') .
                     '</span> caracteres)' : '') . '<br/>' . "\n";
            $html .= '<textarea class="text w100' . (!empty($class) ? ' ' . $class : '') . ($requi ? ' required' : '') .
                     '" name="' . $name . '" id="' . $id .
                     '" rows="10">' . $value . '</textarea>' . "\n";
            if ($configForm->getAttribute('max_lenght') != '') {
                $html .= '<script type="text/javascript">';
                $html .= '$("#' . $id . '").keyup(function(){limitaCampo("' . $name .
                         '", ' . $configForm->getAttribute('max_lenght') . ', "limit_' . $name . '");});';
                $html .= '</script>';
            }
            return $html;
            break;
    }
}


function toCleanName($str, $separator = "_")
{
	$str = strtolower($str);
	$str = clearEmphasis($str);
	$str = str_replace('-', '_', $str);
	$str = str_replace('+', '_', $str);
	$str = str_replace(' ', '_', $str);
	$str = str_replace('__', '_', $str);
	$str = str_replace('__', '_', $str);
	$str = str_replace('_', $separator, $str);
	$patterns[0] = '/[^\w]/';
	$replacements[2] = '';
	return preg_replace($patterns, $replacements, $str);
}


function formatBytesToView($size)
{
	if ($size == '') {
		return '';
	}
	$a = array("bytes", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		$size = $size / 1024;
		$pos = $pos + 1;
	}
	$ret = sprintf('%02d', $size) . ' ' . $a[$pos];
	return $ret;
}


function getFileNameInPath($path)
{
	$path = explode('/', $path);
	return $path[sizeof($path) - 1];
}


function toCleanFileName($str, $separator = "_")
{
	$str = strtolower($str);
	$str = str_replace('-', '_', $str);
	$str = str_replace('+', '_', $str);
	$yummy = array("=", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "{", "}", "[", "]", "|", "\\", "/", "'", ";", ":", '"', "<", ">", ",", "?");
	$healthy = '';
	$str = str_replace($yummy, $healthy, $str);
	$str = trim($str);
	$str = clearEmphasis($str);
	$str = str_replace(' ', '_', $str);
	$str = str_replace('__', '_', $str);
	$str = str_replace('__', '_', $str);
	$str = str_replace('_', $separator, $str);
	return $str;
}


function getYoutubeInfo($id_video) {
    $video = array();

    $doc = new DOMDocument();
    $url = "http://gdata.youtube.com/feeds/api/videos/".$id_video;
    $doc->load($url);

    $xpath = new DomXpath($doc);
    $xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
    $xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
    $xpath->registerNamespace('gd', 'http://schemas.google.com/g/2005');
    $xpath->registerNamespace('yt', 'http://gdata.youtube.com/schemas/2007');

    $video['id']    = $id_video;
    $video['img']   = 'http://i.ytimg.com/vi/'. $id_video .'/0.jpg';
    $video['title'] = $doc->getElementsByTagName("title")->item(0)->nodeValue;

    $duration = $doc->getElementsByTagName("duration");
    foreach ($duration as $tag) {
        $segundos = $tag->getAttribute('seconds');
    }
    $min = (int) ($segundos/60);
    $seg = $segundos-(60*$min);


    $video['duration'] = $min.':'.($seg<10?'0'.$seg:$seg);

    $video['description'] = $doc->getElementsByTagName("description")->item(0)->nodeValue;
    $tags = $doc->getElementsByTagName("category");
    $video['tags'] = array();
    foreach ($tags as $tag) {
        if ($tag->getAttribute('scheme') == 'http://gdata.youtube.com/schemas/2007/keywords.cat') {
            $video['tags'][] = str_replace("'", "", utf8_decode($tag->getAttribute('term')));
        }
    }

    return $video;
}


/**
 * Função que retorna parte de uma string sem cortar os textos
 *
 * @param String  $txt    {Texto que será recortado}
 * @param Integer $limit  {Tamanho máximo do texto}
 * @param Boolean $points {Define se terá os 3 pontos no final do texto}
 *
 * @author Espiral Interativa <ti@espiralinterativa.com>
 * @return String
 */
function cutTextDelimited($txt, $limit, $points = true) {
    if (strlen($txt) > $limit) {
        $ret = substr($txt, 0, $limit);
        $pos = strrpos($ret, ' ');
        if ($pos === false) {
            $txt = $ret;
        } else {
            $ret = substr($ret, 0, $pos);
            $txt = $ret;
        }
    }
    return $txt . ($points ? '...' : '');
}


/**
 * Função que monta um Array com as permissões de visualização e edição de usuários por perfil
 *
 * @param Profiles $profile_active {perfil ativo do usuário a ser liberada as permissões}
 * @param Boolean  $show           {Define se poderão ser exibidos os usuários do perfil}
 * @param Boolean  $edit           {Define se poderão ser alterados os usuários do perfil}
 * @param Profiles $begin_profile  {Define a partir de qual nível de perfis será verificado}
 * @param Array    $arr_profiles   {Array a ser montado}
 *
 * @author Espiral Interativa <ti@espiralinterativa.com>
 * @return Array
 */
function listPermissionsProfiles($profile_active, $show = false, $edit = false, $begin_profile = '', $arr_profiles = '') {
    if (empty($begin_profile)) {
        $begin_profile = new Profiles('dev');
        $arr_profiles  = array();
    }
    if ($profile_active instanceof Profiles && $profile_active->exists() && $begin_profile instanceof Profiles && $begin_profile->exists()) {
        if (count($arr_profiles) == 0) {
            if ($begin_profile->getAttribute('id_profile') == $profile_active->getAttribute('id_profile')) {
                $show = true;
            }
            if ($begin_profile->getAttribute('profile_parent') instanceof Profiles &&
                $begin_profile->getAttribute('profile_parent')->getAttribute('id_profile') == $profile_active->getAttribute('id_profile')) {
                $edit = true;
            }
            $arr_profiles[$begin_profile->getAttribute('id_profile')] = array('show' => $show, 'edit' => $edit);
        }
        $arr_pr = Profiles::getProfiles('', '', '', new Status('a'), $begin_profile);
        if ($arr_pr && count($arr_pr) > 0) {
            foreach ($arr_pr as $profile) {
                if ($profile->getAttribute('id_profile') == $profile_active->getAttribute('id_profile')) {
                    $show = true;
                }
                if ($profile->getAttribute('profile_parent') instanceof Profiles &&
                    $profile->getAttribute('profile_parent')->getAttribute('id_profile') == $profile_active->getAttribute('id_profile')) {
                    $edit = true;
                }
                $arr_profiles[$profile->getAttribute('id_profile')] = array('show' => $show, 'edit' => $edit);
                return listPermissionsProfiles($profile_active, $show, $edit, $profile, $arr_profiles);
            }
        } else {
            return $arr_profiles;
        }
    } else {
        return $arr_profiles;
    }
}


/**
 * Função que monta os filtros de Conteúdos
 *
 * @param ConfigForm $config_form {Configuração de campo utilizado a ser utilizado}
 * @param String     $value       {valor já utilizado no filtro}
 *
 * @author Espiral Interativa <ti@espiralinterativa.com>
 * @return Array
 */
function generationFiltersContent(ConfigForm $config_form, $value = '') {
    $type_html = $config_form->getAttribute('form')->getAttribute('type_html');
    switch ($type_html) {
        case     'input[text]':
        case   'input[hidden]':
        case 'input[password]':
        case     'input[file]':
        case        'textarea':
            $html  = '<br/><br/><label for="' . $config_form->getAttribute('name') . '">' . $config_form->getAttribute('label') . '</label><br/>';
            $html .= '<input type="text" class="text w30" name="' . $config_form->getAttribute('name') . '" value="' . $value . '" />';
            break;
        case 'input[checkbox]':
            $html  = '<br/><br/><span>' . $config_form->getAttribute('label') . '</span><br/>';

            $value  = (is_array($value) ? $value : explode('||', $value));
            $filter = $config_form->getAttribute('form')->getAttribute('filter');
            if ($filter instanceof Filters && $filter->exists()) {
                $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter);
                if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
                    foreach ($arrTerms as $term) {
                        $checked = (in_array($term->getAttribute('id_filter'), $value) ? ' checked="checked"' : '');
                        $html   .= '<input style="vertical-align: middle" id="form_' . $term->getAttribute('id_filter') . '" type="checkbox" class="checkbox" ' .
                                   'name="' . $config_form->getAttribute('name') . '[]"' . $checked .
                                   ' value="' . $term->getAttribute('id_filter') . '"/><label for="form_' . $term->getAttribute('id_filter') . '">' . $term->getAttribute('name_filter') .
                                   '</label>&nbsp;&nbsp;&nbsp;' . "\n";
                    }
                }
            } else {
                $arr_values = explode('|', $config_form->getAttribute('form')->getAttribute('value_default'));
                foreach ($arr_values as $values) {
                    $values  = explode(',', $values);
                    $checked = ($values[0] == $value ? ' checked="checked"' : '');
                    $html   .= '<input style="vertical-align: middle" id="form_' . $values[0] . '" type="checkbox" class="checkbox"' .
                    		   'name="' . $config_form->getAttribute('name') . '[]"' . $checked .
                               ' value="' . $values[0] . '"/><label for="form_' . $values[0] . '">' . $values[1] . '</label>&nbsp;&nbsp;&nbsp;' . "\n";
                }
            }
            break;
        case 'input[radio]':
            $html  = '<br/><br/><span>' . $config_form->getAttribute('label') . '</span><br/>';

            $filter = $config_form->getAttribute('form')->getAttribute('filter');
            if ($filter instanceof Filters && $filter->exists()) {
                $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter);
                if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
                    foreach ($arrTerms as $term) {
                        $checked = ($value == $term->getAttribute('id_filter') ? ' checked="checked"' : '');
                        $html   .= '<input id="form_' . $term->getAttribute('id_filter') . '" type="radio" class="radio"' .
                        		   'name="' . $config_form->getAttribute('name') . '"' . $checked .
                                   ' value="' . $term->getAttribute('id_filter') . '"/><label for="form_' . $term->getAttribute('id_filter') . '">' . $term->getAttribute('name_filter') .
                                   '</label>&nbsp;&nbsp;&nbsp;' . "\n";
                    }
                }
            } else {
                $arr_values = explode('|', $config_form->getAttribute('form')->getAttribute('value_default'));
                foreach ($arr_values as $values) {
                    $values  = explode(',', $values);
                    $checked = ($values[0] == $value ? ' checked="checked"' : '');
                    $html   .= '<input id="form_' . $values[0] . '" type="radio" class="radio"' .
                    		   'name="' . $config_form->getAttribute('name') . '"' . $checked .
                               ' value="' . $values[0] . '"/><label for="form_' . $values[0] . '">' . $values[1] . '</label>&nbsp;&nbsp;&nbsp;' . "\n";
                }
            }
            break;
        case 'select':
            $html  = '<br/><br/><label for="' . $config_form->getAttribute('name') . '">' . $config_form->getAttribute('label') . '</label><br/>';

            $filter = $config_form->getAttribute('form')->getAttribute('filter');
            if ($filter instanceof Filters && $filter->exists()) {
                $childs = 0;
                if ($value == '') {
                    $html .= '<select class="filter clear" name="' . $config_form->getAttribute('name') . '">' . "\n";
                    $html .= '<option value="">Escolha os(as) ' . $config_form->getAttribute('form')->getAttribute('name_form_field') . '</option>' . "\n";
                    $arrTerms = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $filter);
                    if ($arrTerms && is_array($arrTerms) && count($arrTerms) > 0) {
                        foreach ($arrTerms as $term) {
                            $arrchilds = Filters::getFilters(0, 0, 'name_filter', new Status('a'), '', $term);
                            if ($arrchilds) {
                                $childs++;
                            }
                            $selected = ($value == $term->getAttribute('id_filter') ? ' selected="selected"' : '');
                            $html    .= '<option value="' . $term->getAttribute('id_filter') .
                                        '"' . $selected . '>' . $term->getAttribute('name_filter') . '</option>' . "\n";
                        }
                    }
                    $html .= '</select>' . "\n";
                } else {
                    $filter_value = new Filters($value);
                    $arr_html     = generationHtmlSelect($filter_value, array(), '', '', $config_form->getAttribute('name'), '');
                    if (count($arr_html) > 1) {
                        $childs = 1;
                    }
                    krsort($arr_html);
                    foreach ($arr_html as $val) {
                        $html .= $val;
                    }
                }
            } else {
                $arr_values = explode('|', $configForm->getAttribute('form')->getAttribute('value_default'));
                $html      .= '<select name="' . $config_form->getAttribute('name') . '">' . "\n";
                $html      .= '<option value="">Escolha</option>' . "\n";
                foreach ($arr_values as $values) {
                    $values  = explode(',', $values);
                    $checked = ($values[0] == $value ? ' selected="selected"' : '');
                    $html   .= '<option value="' . $values[0] .
                               '"' . $checked . '>' . $values[1] . '</option>' . "\n";
                }
                $html .= '</select>' . "\n";
            }
            break;
    }
    $arr[$config_form->getAttribute('name')] = array(
    	'html' => $html
    );
    return $arr;
}
?>
