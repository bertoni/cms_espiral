<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';
require 'topf.php';
require '../inc/phmagick/phmagick.php';
?>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <?php
    if (!empty($current_dir)) {
        ?>
        <tr>
            <td colspan="2" style="border-bottom:none">
                <p>
                    <b>Upload de arquivos</b>
                    <div id="info">Tamanho máximo: <?= formatBytesToView($POST_MAX_SIZE_BYTES) ?> - Formatos permitidos: <?= strtoupper(implode(', ', $EXTENSIONS_ALLOWED)) ?></div><br />
                </p>

                <form id="file_upload" action="upload.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="dest_dir" value="<?= $current_dir; ?>" />
                    <input type="hidden" name="field" value="<?= $field; ?>" />
                    <input type="hidden" name="type" value="<?= $type; ?>" />

                    <input type="file" name="file" multiple />
                    <button>Upload</button>
                    <div>Selecione</div>
                </form>
                <table id="files"></table>
                <p id="upload_buttons" style="display:none;">
                    <button id="start_uploads" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary">
                        <span class="ui-button-icon-primary ui-icon ui-icon-circle-arrow-e"></span>
                        <span class="ui-button-text">Start Uploads</span>
                    </button>
                    <button id="cancel_uploads" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary">
                        <span class="ui-button-icon-primary ui-icon ui-icon-cancel"></span>
                        <span class="ui-button-text">Cancel Uploads</span>
                    </button>
                </p>

                <script src="fileupload/jquery.min.js"></script>
                <script src="fileupload/jquery-ui.min.js"></script>
                <script src="fileupload/jquery.fileupload.js"></script>
                <script src="fileupload/jquery.fileupload-ui.js"></script>

                <script>
                    $(document).ready(function(){
                        $('#msg_ok').animate({ top: '+=30' }, 400);
                        $('#msg_error').animate({ top: '+=30' }, 400);
                    });

                    $('#file_upload').fileUploadUI({
                        uploadTable: $('#files'),
                        downloadTable: $('#files'),
                        buildUploadRow: function (files, index) {
                            $('#upload_buttons').attr("style", "display:block;margin-top: 5px;");

                            if($.browser.msie == true) {
                                retorno = '<tr>';
                            }else{
                                var retorno = '';
                                var regexpimg = /\.(jpg)|(jpeg)|(gif)|(png)$/i;
                                var regexpdoc = /\.(pdf)|(doc)|(xls)|(txt)|(ppt)|(flv)|(zip)$/i;
                                var regexpaudio = /\.(mp3)|(wmv)$/i;
                                var regexpvideo = /\.(mov)|(avi)$/i;

                                if (regexpimg.test(files[index].name)) {
                                    retorno = '<tr><td align="center">Imagem<\/td>';
                                }else if (regexpdoc.test(files[index].name)){
                                    retorno = '<tr><td align="center">Documento<\/td>';
                                }else if (regexpaudio.test(files[index].name)){
                                    retorno = '<tr><td align="center">Audio<\/td>';
                                }else if (regexpvideo.test(files[index].name)){
                                    retorno = '<tr><td align="center">V�deo<\/td>';
                                }
                            }

                            if($.browser.msie == true) {
                                retorno += '<td>' + files[index].name + '<\/td>' +
                                    '<td class="file_upload_progress"><div><\/div><\/td>' +
                                    '<td class="file_upload_start" style="display:none;">' +
                                    '<button class="ui-state-default ui-corner-all" title="Start Upload">' +
                                    '<span class="ui-icon ui-icon-circle-arrow-e">Start Upload<\/span>' +
                                    '<\/button><\/td>' +
                                    '<td class="file_upload_cancel">' +
                                    '<button class="ui-state-default ui-corner-all" title="Cancel">' +
                                    '<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
                                    '<\/button><\/td><\/tr>';
                            }else{
                                retorno += '<td>' + files[index].name + '<\/td>' +
                                    '<td class="file_upload_progress"><div><\/div><\/td>' +
                                    '<td class="file_upload_start">' +
                                    '<button class="ui-state-default ui-corner-all" title="Start Upload">' +
                                    '<span class="ui-icon ui-icon-circle-arrow-e">Start Upload<\/span>' +
                                    '<\/button><\/td>' +
                                    '<td class="file_upload_cancel">' +
                                    '<button class="ui-state-default ui-corner-all" title="Cancel">' +
                                    '<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
                                    '<\/button><\/td><\/tr>';
                            }

                            return $(retorno);
                        },
                        buildDownloadRow: function (file) {
                            return '';
                        },
                        beforeSend: function (event, files, index, xhr, handler, callBack) {
                            //Formatos permitidos: JPG, JPEG, GIF, PNG, PDF, DOC, XLS, TXT, PPT, FLV, MP3, ZIP, WMV, MOV, AVI
                            var regexp = /\.(jpg)|(jpeg)|(gif)|(png)|(pdf)|(doc)|(xls)|(txt)|(ppt)|(flv)|(mp3)|(zip)|(wmv)|(mov)|(avi)$/i;

                            if (!regexp.test(files[index].name)) {
                                handler.uploadRow.find('.file_upload_progress').html('Este formato de arquivo n�o � permitido.');
                                setTimeout(function () {
                                    handler.removeNode(handler.uploadRow);
                                }, 10000);
                                return;
                            }

                            //Tamanho máximo permitido: 20MB
                            if (files[index].size > 20971520) {
                                handler.uploadRow.find('.file_upload_progress').html('Este arquivo � maior do que o limite permitido.');
                                setTimeout(function () {
                                    handler.removeNode(handler.uploadRow);
                                }, 10000);
                                return;
                            }

                            handler.uploadRow.find('.file_upload_start button').click(callBack);
                        },
                        onComplete: function (event, files, index, xhr, handler) {
                            handler.onCompleteAll(files);
                        },
                        onAbort: function (event, files, index, xhr, handler) {
                            if($('#files tr').length == 1){
                                $('#upload_buttons').attr("style", "display:none;margin-top: 5px;");
                            }

                            handler.removeNode(handler.uploadRow);
                            handler.onCompleteAll(files);
                        },
                        onCompleteAll: function (files) {
                            if ($('#files tr').length == 0) {
                                /* code after all uplaods have completed */
                                window.location.reload( true );
                            }
                        }
                    });

                    $('#start_uploads').click(function () {
                        $('.file_upload_progress .ui-progressbar-value').css('visibility', 'visible');

                        $('.file_upload_start button').click();
                    });
                    $('#cancel_uploads').click(function () {
                        $('.file_upload_cancel button').click();
                    });
                </script>
            </td>
        </tr>
        <?php
    }
    ?>
   	<tr>
       	<td valign="top" style="border-right:none;width:20%;">
            <form method="post" action="create_dir.php">
                <input type="hidden" name="dest_dir" value="<?= $current_dir; ?>" />
                <input type="hidden" name="field" value="<?= $field; ?>" />
                <input type="hidden" name="type" value="<?= $type; ?>" />

                <input style="display:block;float:left;text-align:top;border:1px solid #ddd;margin:10px 0 0 0;padding:5px;width:70%;" type="text" name="dir_name" id="dir_name" size="15" value="" maxlength="30" />
                <input style="display:block;float:left;margin:10px 0 0 0" type="submit" value="" class="input mkdir" />

                <div style="clear:both"></div>
            </form>

            <div id="dir_list">
                <h3 style="margin-bottom:10px"><span style="font-weight:normal">Listando</span> <?php echo ($current_dir==''?'Raiz':$current_dir) ?></h3>
                <?php
                foreach ($files_functions->getDirs() as $file) {
                    $show_folder = false;
                    $is_hidden = false;

                    //if ($rulec->verifyRule(getIdProfile(), 'files_show_hidden_folders')) {
                        $show_folder = true;
                    //}
                    if (!in_array($file['name'], $FOLDERS_HIDE)) {
                        $show_folder = true;
                    } else {
                        $is_hidden = true;
                    }
                    if ($show_folder) {
                        $aditional_text = '';
                        $aditional_link = '';
                        $title = '';
                        $style = '';

                        if ($is_hidden || $is_hidden_current_dir) {
                            $title = ' title="Pasta oculta. Clique para acessar"';
                            $style = ' style="color:#999999;"';
                        } else {
                            $title = ' title="Clique para acessar a pasta"';
                        }

                        if ($file['name'] != '..') {
                            //if (file_exists('delete_dir.php') && $rulec->verifyRule(getIdProfile(), 'files_del_dir')) {
                                //$aditional_link .= '<a href="delete_dir.php?current_dir=' . $current_dir . '&field=' . $field . '&type=' . $type . '&path=' . $file['path'] . '" class="del" onclick="return confirm(\'Você tem certeza que deseja excluir este diretório?\n\n' . $file['path'] . '\')">[X]</a> ';
                                $aditional_link .= '<a href="delete_dir.php?current_dir=' . $current_dir . '&field=' . $field . '&type=' . $type . '&path=' . $file['path'] . '" class="del" onclick="return confirm(\'Você tem certeza que deseja excluir este diretório?\n\n' . $file['path'] . '\')"><img src="icons/exclude.jpg" alt="Excluir" title="Excluir" /></a> ';
                            //}
                            $aditional_text .= '/';
                        } else {
                            $aditional_text .= '/';
                            $title = ' title="Clique para voltar"';
                            #$style = ' style="color:#999999;"';
                        }
                        echo '<p>' . $aditional_link . '<a class="dir" href="files.php?current_dir=' . $file['path'] . '&field=' . $field . '&type=' . $type . '"' . $title . '' . $style . '>' . $file['name'] . '' . $aditional_text . '</a></p>';
                    }
                }
                ?>
            </div>
            <div id="img_view"></div>
        </td>
        <td style="background-color: #fafafa" valign="top" width="70%">
            <div id="file_list">


                <?php
                $show_images = false;
                $show_videos = false;
                $show_docs = false;
                if ($type == '') {
                    $show_images = true;
                    $show_videos = true;
                    $show_docs = true;
                }
                if ($type == 'tagimg') {
                    $show_images = true;
                } else if ($type == 'linkgeneral') {
                    $show_images = true;
                    $show_videos = true;
                    $show_docs = true;
                } else if ($type == 'path') {
                    $show_images = true;
                    $show_videos = true;
                    $show_docs = true;
                }
                foreach ($files_functions->getFiles(true) as $file) {
                    $aditional_atribs = '';
                    $aditional_actions = '';
                    $is_param_file = false;
                    if ($param_file_name == $file['name']) {
                        $is_param_file = true;
                    }
                    $root_path = FILES_ROOT_PATH . '' . $current_dir . '/' . $file['name'];
                    $file_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $root_path);
                    $file_path = str_replace('//', '/', $file_path);
                    $href_select = '';
                    $tag = '';
                    $str_thumb = '';
                    $image_full_x = 0;
                    $image_full_y = 0;
                    $is_image = false;
                    if (in_array($file['file_ext'], $EXTENSIONS_IMAGES)) {
                        $is_image = true;
                    }
                    $is_video = false;
                    if (in_array($file['file_ext'], $EXTENSIONS_VIDEOS)) {
                        $is_video = true;
                    }
                    $is_doc = false;
                    if (in_array($file['file_ext'], $EXTENSIONS_DOCS)) {
                        $is_doc = true;
                    }
                    $is_pdf = false;
                    if ($file['file_ext'] == 'pdf') {
                        $is_pdf = true;
                    }

                    if ($show_images) {
                        if ($is_image) {
                            $image_full_info = getimagesize($root_path);
                            $image_full_x = $image_full_info[0];
                            $image_full_y = $image_full_info[1];

                            $size = filesize($root_path);

                            if (!file_exists(FILES_ROOT_PATH_THUMBS . $size . $file['name'])) {
                                $tb = new phMagick(FILES_ROOT_PATH . $current_dir . '/' . $file['name'], FILES_ROOT_PATH_THUMBS . $size . $file['name']);
                                $tb->resizeExactly(95, 95);
                            }
                            $caminho_icone = PATH_CMS_DOC_ROOT . 'files/icons/' . $file['file_ext'] . '.gif';

                            $str_thumb = '<span style="padding-left:16px; background:url(' . str_replace(PATH_CMS_DOC_ROOT . 'files/', '', $caminho_icone) . ') top left no-repeat; display:block;">&nbsp;</span>';
                            //$str_thumb .= '<div style="background:#f4f4f4;height:95px;width:95px;" class="image-container"><img style="margin:0;padding:0;display:block;" src="' . PATH_UPLOADS_THUMBS . $file['name'] . '"></div>';
                            $str_thumb .= '<div style="background:#f4f4f4 url(' . PATH_UPLOADS_THUMBS . $size . $file['name'] . ') center center no-repeat;height:95px;width:95px;" class="image-container"></div>';
                            $str_thumb .= '<p>'.$file['name'].'</p>';

                        } else {
                            $caminho_icone       = PATH_CMS_DOC_ROOT . '/files/icons/' . $file['file_ext'] . '.gif';
                            $caminho_icone_large = PATH_CMS_DOC_ROOT . '/files/icons/large/' . $file['file_ext'] . '.png';

                            //echo $caminho_icone;exit;

                            if (file_exists($caminho_icone)) {
                                $str_thumb = '<span style="padding-left:16px; background:url('.str_replace(PATH_CMS_DOC_ROOT.'/files/','',$caminho_icone).') top left no-repeat; display:block;"></span>';
                            } else {
                                $str_thumb = '<span>' . $file['name'] . '</span>';
                                #$str_thumb = '<span style="width:' . FILES_THUMB_SIZE_X . 'px; height:' . FILES_THUMB_SIZE_Y . 'px;"></span>';
                            }

                            if (file_exists($caminho_icone_large)) {
                                //$str_thumb .= '<img src="/_cms/files/icons/large/' . $file['file_ext'] . '.png' . '">';
                            } else {
                                //$str_thumb .= '<img src="/_cms/files/icons/large/unknown.png' . '">';
                            }

                            $str_thumb .= '<div style="background:#f4f4f4 url(/_cms/files/icons/large/unknown.png) center center no-repeat;height:95px;width:95px;" class="image-container"></div>';

                            $str_thumb .= '<p>'.$file['name'].'</p>';
                        }

                        if ($is_pdf) {

                            $size = filesize($root_path);

                            if (!file_exists(FILES_ROOT_PATH_THUMBS . $size . $file['name'] . '.jpg')) {
                                $pdf_thumb = new phMagick(FILES_ROOT_PATH . $current_dir . '/' . $file['name'], FILES_ROOT_PATH_THUMBS . $size . $file['name'] . '.jpg');
                                $pdf_thumb->acquireFrame(FILES_ROOT_PATH . $current_dir . '/' . $file['name']);

                                $pdf_thumb = new phMagick(FILES_ROOT_PATH_THUMBS . $size . $file['name'] . '.jpg', FILES_ROOT_PATH_THUMBS . $size . $file['name'] . '.jpg');
                                $pdf_thumb->resizeExactly(95, 95);

                            }
                            $str_thumb = '<span style="padding-left:16px; background:url(' . str_replace(PATH_CMS_DOC_ROOT . '/files/', '', $caminho_icone) . ') top left no-repeat; display:block;">&nbsp;</span>';
                            //$str_thumb .= '<img src="' . PATH_UPLOADS_THUMBS . $file['name'] . '.jpg' . '">';
                            $str_thumb .= '<div style="background:#f4f4f4 url(' . PATH_UPLOADS_THUMBS . $size . $file['name'] . '.jpg) center center no-repeat;height:95px;width:95px;" class="image-container"></div>';
                            $str_thumb .= '<p>'.$file['name'].'</p>';
                        }
                    }
                    if ($type == 'tagimg') {
                        $href_select = 'image_atributes.php?field=' . $field . '&path=' . $file_path . '';
                    } else if ($type == 'linkgeneral') {
                        $tag = '<a href=#' . $file_path . '#>' . $file['name'] . '</a>';
                        $href_select = 'javascript: void(sendTag(\'' . $field . '\', \'' . $tag . '\'))';
                    } else if ($type == 'path') {
                        $tag = '' . $file_path . '';
                        $href_select = 'javascript: void(sendPath(\'' . $field . '\', \'' . $tag . '\'))';
                    }
                    if (!empty($href_select)) {
                        //$aditional_actions .= '<a href="' . $href_select . '">[selecionar]</a>&nbsp;';
                        $aditional_actions .= '<a class="icon" href="' . $href_select . '"><img alt="" src="/_cms/img/icon.relate.png" /></a>&nbsp;';
                    }
                    if ($show_images && $is_image) {
                        //if (file_exists('crop.php') && $rulec->verifyRule(getIdProfile(), 'files_crop_images')) {
                            $aditional_actions .= '<a class="icon show-crop" href="crop.php?current_dir=' . $current_dir . '&field=' . $field . '&type=' . $type . '&file_name=' . $file['name'] . '" class="crop"><img alt="" src="/_cms/img/icon.cut.png" /></a>';
                        //} else {
                        //    $aditional_actions .= '<a class="icon" class="crop"><img alt="" src="/_cms/img/icon.cut.disabled.png" /></a>';
                        //}
                    } else {
                        $aditional_actions .= '<a class="icon" class="crop"><img alt="" src="/_cms/img/icon.cut.disabled.png" /></a>';
                    }
                    /*if (
                            ($is_hidden_current_dir == false && file_exists('delete_file.php') && $rulec->verifyRule(getIdProfile(), 'files_del_file'))
                            ||
                            ($is_hidden_current_dir && file_exists('delete_file.php') && $rulec->verifyRule(getIdProfile(), 'files_del_files_hidden_folders'))
                    ) {*/
                        //$aditional_actions .= '<a href="delete_file.php?current_dir=' . $current_dir . '&field=' . $field . '&type=' . $type . '&file=' . $file['name'] . '" class="del" onclick="return confirm(\'Você tem certeza que deseja excluir este arquivo?\n\n' . $file['name'] . '\')">[excluir]</a> ';
                        $aditional_actions .= '<a class="icon" href="delete_file.php?current_dir=' . $current_dir . '&field=' . $field . '&type=' . $type . '&file=' . $file['name'] . '" class="del" onclick="return confirm(\'Você tem certeza que deseja excluir este arquivo?\n\n' . $file['name'] . '\')"><img alt="" src="/_cms/img/icon.delete.dark.png" /></a> ';
                    //}

                    if ($show_images && $is_image) {
                        echo '<div class="file image' . ( ($is_param_file) ? ' destak' : '' ) . '"  title="' . $file['name'] . ' - ' . formatBytesToView(filesize($root_path)) . '">';
                        echo '<a href="' . $file_path . '" rel="files" class="preview lightbox" title="' . $file['name'] . ' - ' . formatBytesToView(filesize($root_path)) . ' - ' . $image_full_x . 'x' . $image_full_y . '" ' . $aditional_atribs . '>' . $str_thumb . '</a>';
                        echo '<br />' . $aditional_actions . '';
                        //echo '<a href="/banco-de-imagens/download.php?file=/uploads/' . $current_dir . '/' . $file['name'] . '" >[download]</a>';
                        echo '<a class="icon" href="/banco-de-imagens/download.php?file=/uploads/' . $current_dir . '/' . $file['name'] . '" ><img title="Download" alt="" src="/_cms/img/icon.save.png" /></a>';

                        echo '</div>';
                    } else if ($show_docs && $is_doc) {
                        //echo '<div style="clear:both;"></div>';
                        echo '<div class="file doc' . ( ($is_param_file) ? ' destak' : '' ) . '" title="' . $file['name'] . ' - ' . formatBytesToView(filesize($root_path)) . '">';
                        echo '<a href="' . $file_path . '" ' . $aditional_atribs . '>' . $str_thumb . '</a>';
                        echo '<br/>' . $aditional_actions . '';
                        //echo '<a href="/banco-de-imagens/download.php?file=/uploads/' . $current_dir . '/' . $file['name'] . '" >[download]</a>';
                        echo '<a class="icon" href="/banco-de-imagens/download.php?file=/uploads/' . $current_dir . '/' . $file['name'] . '" ><img title="Download" alt="" src="/_cms/img/icon.save.png" /></a>';

                        echo '</div>';
                        //echo '<div style="clear:both;"></div>';
                    } else if ($show_videos && $is_video) {
                        //echo '<div style="clear:both;"></div>';
                        echo '<div class="file video' . ( ($is_param_file) ? ' destak' : '' ) . '" title="' . $file['name'] . ' - ' . formatBytesToView(filesize($root_path)) . '">';
                        echo '<a href="' . $file_path . '" ' . $aditional_atribs . '>' . $str_thumb . '</a>';
                        echo '<br/>' . $aditional_actions . '';
                        //echo '<a href="/banco-de-imagens/download.php?file=/uploads/' . $current_dir . '/' . $file['name'] . '" >[download]</a>';
                        echo '<a class="icon" href="/banco-de-imagens/download.php?file=/uploads/' . $current_dir . '/' . $file['name'] . '" ><img title="Download" alt="" src="/_cms/img/icon.save.png" /></a>';
                        echo '</div>';
                        //echo '<div style="clear:both;"></div>';
                    }
                }
                ?>
            </div>
        </td>
    </tr>
</table>
<?php require 'botf.php'; ?>