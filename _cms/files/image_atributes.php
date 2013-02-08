<?php
require 'topf.php';

$path = $_GET['path'];
?>

    <form onsubmit="return mountTag('<?=$field;?>', this);">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    	<tr>
        	<td colspan="2">
            	<div style="max-height:250px; overflow:auto;">
		    <center><img src="<?=$path;?>" /></center>
                    <input type="hidden" name="path" id="path" value="<?=$path;?>" />
                </div>
            </td>
        </tr>
        <tr>
        	<td colspan="2">
            	<label for="title">Title</label>
                <input type="text" name="title" id="title" size="70" />
            </td>
        </tr>
        <tr>
        	<td width="50%">
            	<label for="align">Align</label>
                <select name="align" id="align">
                    <option value="">--</option>
                    <option value="left">Left</option>
                    <option value="right">Right</option>
                    <option value="texttop">Texttop</option>
                    <option value="absmiddle">Absmiddle</option>
                    <option value="baseline">Baseline</option>
                    <option value="absbottom">Absbottom</option>
                    <option value="bottom">Bottom</option>
                    <option value="middle">Middle</option>
                    <option value="top">Top</option>
                </select>
            </td>
            <td>
            	<label for="border">Border</label>
                <input type="text" name="border" id="border" />
            </td>
        </tr>
        <tr>
        	<td width="50%">
            	<label for="hspace">Hspace</label>
                <input type="text" name="hspace" id="hspace" />
            </td>
            <td>
            	<label for="vspace">Vspace</label>
                <input type="text" name="vspace" id="vspace" />
            </td>
        </tr>
        <tr>
        	<td colspan="2" align="right">
            	<input type="submit" value="enviar" />
            </td>
        </tr>
    </table>
    </form>

<?php
require 'botf.php';
?>