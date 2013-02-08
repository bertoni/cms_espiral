<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';
require 'topf.php';
$file_name = $_GET['file_name'];
$x=0;$y=0;
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="/js/mylibs/jcrop/jquery.Jcrop.min.js"></script>

<script>
    //Inicia a váriavel para o Zoom
    zoom = 1;

    //Atualiza os inputs hidden com os valores da seleção
    function updateCoords(coords) {
        $('#x1').val(coords.x/zoom);
        $('#y1').val(coords.y/zoom);
        $('#x2').val(coords.x2/zoom);
        $('#y2').val(coords.y2/zoom);
        $('#width').val(coords.w/zoom);
        $('#height').val(coords.h/zoom);
    }

    //Aplica o zoom e define a proporção para a área selecionavel
    function updateRecorteArea() {

        //Aplica o zoom
        $('#zoom').val(zoom*100);
        $('#tocrop').width(tocrop_w*zoom);
        $('#tocrop').height(tocrop_h*zoom);

        if ($('#option').val() != '0x0') {
            $("#zoom").attr("disabled", "disabled");
            var dimensions = $('#option').val().split("x")
            $('#x').val(dimensions[0]);
            $('#y').val(dimensions[1]);

			
            $('#tocrop').Jcrop({
                onChange: updateCoords,
                bgColor: 'black',
                bgOpacity: .4,
                setSelect: [ 1, 1, dimensions[0]*zoom, dimensions[1]*zoom ],
                aspectRatio: dimensions[0]/dimensions[1]
            });
            $('#submit').fadeIn();
        }
    }

    //Funções para arrumar o bug das imagens no webkit
    function getOriginalWidth(imagem) {
        var t = new Image();
        t.src = (imagem.attr('src'));
        return t.width;
    }
    function getOriginalHeight(imagem) {
        var t = new Image();
        t.src = (imagem.attr('src'))
        return t.height;
    }

    $(document).ready(function() {
        $('#tocrop').load(function(){
            //Verifica se a imagem é enorme comparada à janela
            tocrop_w = getOriginalWidth($('#tocrop'));
            tocrop_h = getOriginalHeight($('#tocrop'));

            window_w = $(window).width();
            window_h = $(window).height();

            //Define o zoom inicial
            if (tocrop_w>window_w || tocrop_h>window_w) {
                zoom = 0.5;
                if ((tocrop_w/2)>window_w || (tocrop_h/2)>window_h ) {
                    zoom = 0.25;
                }
                updateRecorteArea();
            }
        });

        //Atualiza o zoom
        $('#zoom').change(function() {
            zoom = parseFloat($(this).val()/100);
            updateRecorteArea();
        });

        $('#option').change(function() {
            updateRecorteArea();
        });
    });
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100px" align="center">
			<input type="button" class="input" value="cancelar" onclick="location.href='files.php?current_dir=<?= $current_dir ?>&field=<?= $field ?>&type=<?= $type ?>&msg_ok=Recorte cancelado'">
		</td>

		<td>
			<form method="post" action="crop_proc.php">
				<input type="hidden" name="current_dir" value="<?=$current_dir;?>" />
                <input type="hidden" name="file_name" value="<?=$file_name;?>" />
                <input type="hidden" name="field" value="<?=$field;?>" />
                <input type="hidden" name="type" value="<?=$type;?>" />
                <input type="hidden" name="x" id="x" value="<?=$x;?>" />
                <input type="hidden" name="y" id="y" value="<?=$y;?>" />

                <input type="hidden" name="x1" id="x1" />
                <input type="hidden" name="y1" id="y1" />
                <input type="hidden" name="x2" id="x2" />
                <input type="hidden" name="y2" id="y2" />
                <input type="hidden" name="width" id="width" />
                <input type="hidden" name="height" id="height" />

                Zoom: <select id="zoom" name="zoom" class="input" style="width:100px;text-align:left;">
                    <option value="400">400%</option>
                    <option value="200">200%</option>
                    <option value="100" selected>100%</option>
                    <option value="50">50%</option>
                    <option value="25">25%</option>
                </select>
                &nbsp;&nbsp;
                Tamanho da imagem: <select name="option" id="option" class="input" style="width:300px;text-align:left;">
                <?php
                foreach ($IMAGE_CUT_SIZES as $key=>$value) {
                    echo '<option value="'.$value['x'].'x'.$value['y'].'">' . $value['text'] . '</option>';
                }
                ?>
                </select>
                &nbsp;&nbsp;&nbsp;
                <input type="submit" id="submit" style="width:150px;vertical-align:bottom;display:none" value="recortar seleção" class="input ibt_save" />

                <div class="vc"></div><br/>
                * Ao recortar uma imagem, a original não será afetada e continuará gravada no sistema.<br/>
                * Se a imagem for muito grande o zoom será automaticamente aplicado, o zoom só pode ser usado antes de selecionar o tamanho da imagem.<br/>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<img id="tocrop" src="<?= PATH_UPLOADS ?><?= $current_dir ?>/<?= $file_name ?>">
		</td>
	</tr>
</table>

<?php require 'botf.php'; ?>