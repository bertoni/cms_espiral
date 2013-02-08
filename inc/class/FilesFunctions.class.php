<?php
class FilesFunctions {

	public $root_path;
	public $dir_rel_path;

	function FilesFunctions($root_path = FILES_ROOT_PATH) {
		if ($root_path[strlen($root_path)-1] != '/') {
			$root_path = $root_path . '/';
		}
		$this->setRootPath($root_path);
		$this->setDirRelPath('');
	}


	function setDirRelPath($dir_rel_path) {
		# Security
		if (strpos($dir_rel_path, '.') === false ) {
			$this->dir_rel_path = $dir_rel_path;
		}
	}


	function getDirRelPath() {
		return $this->dir_rel_path;
	}


	function setRootPath($root_path) {
		$this->root_path = $root_path;
	}


	function getRootPath() {
		return $this->root_path;
	}


	function getAbsDirPath() {
		if ($this->getDirRelPath() == '') {
			return $this->getRootPath();
		} else {
			return $this->getRootPath() . $this->getDirRelPath() . '/';
		}
    }


    function sksort(&$array, $subkey="id", $sort_ascending=false) {

        $temp_array = array();

        if (count($array))
            $temp_array[key($array)] = array_shift($array);

        foreach($array as $key => $val){
            $offset = 0;
            $found = false;
            foreach($temp_array as $tmp_key => $tmp_val)
            {
                if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
                {
                    $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                                                array($key => $val),
                                                array_slice($temp_array,$offset)
                                              );
                    $found = true;
                }
                $offset++;
            }
            if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
        }

        if ($sort_ascending) $array = array_reverse($temp_array);

        else $array = $temp_array;
    }


	function getDirs() {
		$arr_re = array();
		$d = dir($this->getAbsDirPath());
		#print_r($d);
		#echo 'x=' . $d->path . '<br />';
		#echo 'y=' . $this->getRootPath() . '<br />';
		$nd = substr($d->path, strlen($this->getRootPath()));
		#echo 'nd=' . $nd . '<br />';
		if ($nd != '') {
			$nd_arr = split('/',$nd);
			array_pop($nd_arr);
			array_pop($nd_arr);
			$back = implode("/", $nd_arr);
			#print_r($nd_arr);
			array_push($arr_re, array('path'=>$back, 'name'=>'..') );
		}

		while (false !== ($entry = $d->read())) {
			if (is_dir($this->getAbsDirPath() . $entry)) {
				if (($entry != '.') && ($entry != '..')) {
                                        $last_modified = filemtime($this->getAbsDirPath() . $entry);
					array_push($arr_re, array('path'=>$nd . $entry, 'name'=>$entry, 'last_modified'=>$last_modified) );
				}
			}
		}

                #$this->sksort($arr_re, 'last_modified', false);
                $this->sksort($arr_re, 'name', true);

		$d->close();

		return $arr_re;
	}


	function getFileExt($filename) {
		$filename = strtolower($filename) ;
		$arr = split('\.', $filename) ;
		$n = count($arr)-1;
		return $arr[$n];
	}


	function getFiles($date_sort = false) {
		global $EXTENSIONS_ALLOWED;
		$arr_re = array();
		$d = dir($this->getAbsDirPath());
		while (false !== ($entry = $d->read())) {
			if (is_file($this->getAbsDirPath() . $entry)) {
				$file_ext = $this->getFileExt($entry);
				if (in_array($file_ext, $EXTENSIONS_ALLOWED)) {
                                        $last_modified = filemtime($this->getAbsDirPath() . $entry);
					array_push($arr_re, array('name'=>$entry, 'file_ext'=>$file_ext, 'last_modified'=>$last_modified));
				}
			}
		}
		$d->close();
                if($date_sort) {
                    $this->sksort($arr_re, 'last_modified', false);
                }
		return $arr_re;
	}


	function createThumb($path_file) {
	    /*global $EXTENSIONS_IMAGES;

	    $file_info = getimagesize($path_file);
	    $width = $file_info[0];
	    $height = $file_info[1];
	    $mimetype = $file_info['mime'];
	    $extension = $this->getFileExt($path_file);

	    if (in_array($extension, $EXTENSIONS_IMAGES)) {
		#print_r($file_info);

		$newwidth = FILES_THUMB_SIZE_X;
		$newheight = FILES_THUMB_SIZE_Y;
		if ($width > $newwidth) {
		    #altura proporcional
		    #$newheight=($height/$width)*$newwidth;

		    $file_name_only = getFileNameInPath($path_file);
		    $new_path = str_replace($file_name_only, '', $path_file);
		    $new_path .= FILES_THUMB_PREFIX . $file_name_only;

		    $tmp=imagecreatetruecolor($newwidth,$newheight);
		    if (copy($path_file, $new_path)) {
			$src = imagecreatefromjpeg($new_path);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			imagejpeg($tmp, $new_path, 60);
			#echo $this->getRootPath();

			move_uploaded_file($tmp, $new_path);
		    }
		}

		return true;
	    } else {
		return false;
	    }*/
	}


}
?>