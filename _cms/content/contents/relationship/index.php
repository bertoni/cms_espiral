<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!isset($_GET['id'])) {
    header('Location: /_cms/');
    exit;
} else {
    $content = new Contents($_GET['id']);
    if (!$content->exists()) {
        header('Location: /_cms/');
        exit;
    }
    $typeContent = $content->getAttribute('type_content');
    if (!$typeContent->exists() || $typeContent->getAttribute('status')->getAttribute('id_status') != 'a') {
        header('Location: /_cms/');
        exit;
    }
}

$module_code = 'cont';
$page_code   = (isset($_GET['cont']) ? $_GET['cont'] : '');

require_once 'actions_rules_content.inc.php';
if (!$RULE['lis']) {
    header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão de ver este conteúdo') . '.');
    exit;
}

require 'top.inc.php';
?>
<h1><?php echo $i18n->_('Relacionar Conteúdos à'); ?> <?php echo $typeContent->getAttribute('name_type_content'), ' > ', $content->getAttribute('title'); ?></h1>

<form name="form_search" action="" method="get">

	<label for="type_content"><?php echo $i18n->_('Tipo de Conteúdo'); ?></label><br/>
	<select class="text required w30" name="type_content" id="type_content">
		<option value=""><?php echo $i18n->_('Escolha o Tipo de Conteúdo'); ?></option>
		<?php
		$arr_type_contents = TypeContents::getTypeContents('', '', 'name_type_content', new Status('a'));
		if ($arr_type_contents && count($arr_type_contents) > 0) {
    		foreach ($arr_type_contents as $key=>$obj_type_content) {
    		    if (TypeContents::checkTypeContentRTypeContent($typeContent, $obj_type_content)) {
    			    echo '<option value="' . $obj_type_content->getAttribute('id_type_content') . '">' . $obj_type_content->getAttribute('name_type_content') . '</option>';
    		    }
    		}
		}
		?>
	</select>

</form>

<hr class="clear"/>

<h2><?php echo $i18n->_('Conteúdos Relacionados'); ?></h2>
<?php
$arr_relationship = Contents::getContentsRelationship($content, true);
echo '
<table>
	<thead>
	<tr>
		<th>' . $i18n->_('Tipo de Conteúdo') . '</th>
		<th>' . $i18n->_('Título') . '</th>
		<th>' . $i18n->_('Status') . '</th>
		<th width="1%">' . $i18n->_('Ações') . '</th>
	</tr>
	</thead>
	<tbody class="list-relacionados">';
if ($arr_relationship && sizeof($arr_relationship) != 0) {
	foreach ($arr_relationship as $key=>$content_rel) {
	    echo '
	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
	    	<td>' . $content_rel->getAttribute('type_content')->getAttribute('name_type_content') . '</td>
	    	<td>' . $content_rel->getAttribute('title') . '</td>
	    	<td>' . $content_rel->getAttribute('status')->getAttribute('name_status') . '</td>
	    	<td><a class="button small unlink" href="' . $content_rel->getAttribute('id_content') . '">' . $i18n->_('Remover') . '</a></td>
	    </tr>
	    ';
	}
}
echo '
	</tbody>
</table>';
?>
<hr/>

<h2><?php echo $i18n->_('Conteúdos possíveis de relacionamento'); ?></h2>

<table>
	<thead>
    	<tr>
    		<th><?php echo $i18n->_('Tipo de Conteúdo'); ?></th>
    		<th><?php echo $i18n->_('Título'); ?></th>
    		<th><?php echo $i18n->_('Status'); ?></th>
    		<th width="1%"><?php echo $i18n->_('Ações'); ?></th>
    	</tr>
	</thead>
	<tbody class="list-relacionaveis">
	</tbody>
</table>
<a class="button small" id="btn-mais-results" style="float: right;margin-top: 20px;display: none;" href="#"><?php echo $i18n->_('Listar Conteúdos Restantes'); ?> (<span id="rest"></span>)</a>


<script type="text/javascript">
$(document).ready(function(){

	$('.list-relacionados a').live('click', function(e){
		e.preventDefault();
		var parent = <?php echo $content->getAttribute('id_content'); ?>;
		var child  = $(this).attr('href');
		var line   = $(this).parent().parent();
		$.ajax({
            type: 'POST',
            url: '/inc/functions_ajax.inc.php',
            data: 'parent=' + parent + '&child=' + child + '&rule=' + false + '&action=changeContentsRContents',
            dataType: 'json',
            async: true,
            success: function(data) {
				if (data.status == '1') {
					line.remove();
				} else {
					setaMsgGeral('alert', data.msg);
				}
            }
        });
	});

	$('.list-relacionaveis a').live('click', function(e){
		e.preventDefault();
		var parent = <?php echo $content->getAttribute('id_content'); ?>;
		var child  = $(this).attr('href');
		var line   = $(this).parent().parent();
		var link   = $(this);
		$.ajax({
            type: 'POST',
            url: '/inc/functions_ajax.inc.php',
            data: 'parent=' + parent + '&child=' + child + '&rule=' + true + '&action=changeContentsRContents',
            dataType: 'json',
            async: true,
            success: function(data) {
				if (data.status == '1') {
					link.addClass('unlink');
					link.html('Remover');
					$('.list-relacionados').append(line);
					$('.list-relacionaveis').remove(line);
				} else {
					setaMsgGeral('alert', data.msg);
				}
            }
        });
	});

	function appendResults(value, begin) {
		$.ajax({
            type: 'POST',
            url: '/inc/functions_ajax.inc.php',
            data: 'id=' + value + '&status=' + true + '&begin=' + begin + '&end=' + 2 + '&action=getContentsByTypeContent',
            dataType: 'json',
            async: true,
            success: function(data) {
				if (data.status == '1') {
					$.each(data.contents, function(i, content){
						var html = '';
						html    += '<tr' + (i%2 == 0 ? ' class="even"' : '') + '>';
						html    += '<td>' + content.type_content + '</td>';
						html    += '<td>' + content.title + '</td>';
						html    += '<td>' + content.status + '</td>';
						if (content.id != <?php echo $content->getAttribute('id_content'); ?>) {
							html += '<td><a class="button small" href="' + content.id + '"><?php echo $i18n->_('Relacionar'); ?></a></td>';
						} else {
							html += '<td></td>';
						}
						html += '</tr>';
						$('.list-relacionaveis').append(html);
					});
					if (data.restantes != 0) {
    					$('#btn-mais-results').show();
    					$('#rest').html(data.restantes);
    					$('#btn-mais-results').removeClass('unlink');
					} else {
						$('#btn-mais-results').show();
    					$('#rest').html(data.restantes);
    					$('#btn-mais-results').addClass('unlink');
					}
				} else {
					setaMsgGeral('alert', data.msg);
					$('#rest').html('0');
					$('#btn-mais-results').addClass('unlink');
				}
            }
        });
	}

	$('#btn-mais-results').click(function(e){
		e.preventDefault();
		if ($(this).hasClass('unlink') == false) {
			var value = $('select[name="type_content"]').val();
			var begin = $('.list-relacionaveis tr').size();
			appendResults(value, begin);
		}
	});

	$('select[name="type_content"]').change(function(){
		if ($(this).val() != '') {
			$('.list-relacionaveis').html('');
			$('#rest').html('');
			$('#btn-mais-results').hide();
			
			var value = $(this).val();
			appendResults(value, 0);
		}
	});
	
});
</script>

<?php require 'bot.inc.php'; ?>