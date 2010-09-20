<?php
	// functions for file download handling //

	// scan the tmp-downloads folder and delete files that are too old
	function ScanTempDownloads($maxage = 43200 /* = 12h */, $currentdir = false, $level = 0) {
		$tmpdir = cms_join_path(dirname(dirname(dirname(__FILE__))), 'tmp', 'downloads');
		$currentdir = ($currentdir === false) ? $tmpdir : $currentdir;
		$delete = array();
		$dir = opendir($currentdir);

		while (false !== ($filename = readdir($dir))) {
			if($filename != '..' && $filename != '.') {
				$file = cms_join_path($currentdir, $filename);

				if(is_dir($file)) {
					++$level;
					ScanTempDownloads($maxage, $file, $level);
				} else {
					if(time() - filemtime($file) > $maxage) {
						@unlink($file);
						if($level > 0) {
							$dirname = substr($file, 0, strrpos($file, DIRECTORY_SEPARATOR));
							$delete[] = $dirname;
						}
					}
				}
			}
		}

		closedir($dir);
		foreach($delete as $dir) {rmdir($dir);}
	}

	// acts similar to readfile but chunks the file into parts of 1MB and sends it directly to the client - good for bigger files
	function readfile_chunked($filename,$retbytes=true) {
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();flush();
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
			$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;

	}

	// validations functions //
	// validate the url based on a Regular Expression
	function ValidateURL($url) {
		$pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		if (preg_match($pattern, $url))
			return true;
		else
			return false;
	}

	// misc //
	// format the given filesize (in bytes)
	function FormatFilesize($size, $prefix = true, $short = true){
		if($prefix === true) {
			if($short === true) {
				$norm = array('Byte', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			} else {
				$norm = array('Byte', 'Kilobyte', 'Megabyte', 'Gigabyte', 'Terabyte', 'Petabyte', 'Exabyte', 'Zettabyte',  'Yottabyte');
			}

			$factor = 1000;
		} else {
			if($short === true) {
				$norm = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
			} else {
				$norm = array('Byte', 'Kibibyte', 'Mebibyte', 'Gibibyte', 'Tebibyte', 'Pebibyte', 'Exbibyte', 'Zebibyte', 'Yobibyte');
			}

			$factor = 1024;
		}

		$count = count($norm) -1;

		$x = 0;
		while ($size >= $factor && $x < $count)
		{
			$size /= $factor;
			++$x;
		}

		$size = sprintf("%01.2f", $size) . ' ' . $norm[$x];

		return $size;
	}

	// filename, -extension and -identifier
	function FileInfo($location) {
		$info = array();

		if(substr($location, 0, 2) == '$$') {
			$location = substr($location, 2);
			if(strpos($location, ID_SEPARATOR) === false) {
				$info['filename'] = substr($location, 0, strrpos($location, '.'));
				$info['identifier'] = '';
			} else {
				$info['identifier'] = substr($location, strrpos($location, ID_SEPARATOR)+1, strrpos($location, '.') - strrpos($location, ID_SEPARATOR)-1);
				$info['filename'] = substr($location, 0, strrpos($location, ID_SEPARATOR));
			}
		} else {
			$info['filename'] = substr($location, strrpos($location, '/')+1, strrpos($location, '.') - strrpos($location, '/')-1);
		}

		$info['fileext'] = strrchr($location, '.');

		return $info;
	}

	// split the template using TPL_SEPARATOR
	function SplitTemplate($tpl_content, $separator = TPL_SEPARATOR){
		return explode($separator, $tpl_content);
	}

	// returns all files  within the specified folder, optionally filtered by file extension
	function ListDir($d,$x=''){
		foreach(array_diff(scandir($d),array('.','..')) as $f)if(is_file($d.'/'.$f)&&(($x)?preg_match('/'.$x.'$/',$f):1))$l[]=$f;
		return $l;
	}

	// display an image from /modules/DLM/images/
	function DisplayImage($imageName, $alt='', $title='', $valign = 'middle', $class='', $style='') {
		global $gCms;
		$config =& $gCms->config;
		$img = $config['root_url'] . '/modules/DLM/images/'. $imageName;

		$valign = ($valign === false) ? '' : 'vertical-align: '.$valign . ';';

		return '<img src="'.$img.'" alt="'.$alt.'" style="'.$valign.$style.'" '.($title=='' ? '' : 'title="'.$title.'" ').($class=='' ? '' : 'class="'.$class.'" ').'/>';
	}

	// returns the pretty-url for the given data
	function MakePretty($item, $returnid = false, $junk = false) {
		$returnid = false;
		$url = 'dlm/'.$item.(($returnid !== false) ? '/'.$returnid : '').(($junk !== false) ? '/'.munge_string_to_url($junk) : '');
		return $url;
	}

	// some extra CSS for the admin-panel
	function GetAdminStyle() {
		return str_replace('	', '', str_replace("\r\n", '', '
		body.wait * {cursor: wait !important}
		div.pagemcontainer p {margin:0}

		#dlmpage .checked.row2 {background-color: #8CE334 !important}
		#dlmpage .checked.row1 {background-color: #CFFF7D !important}
		#dlmpage .checked:hover {background-color: #7CC92E !important}

		#dlmpage .pagetable th {height: 10px !important}

		#mirrors li{cursor:move;background:#fff;padding:5px;margin:0 10px 10px 0;border:1px solid #ABC6DD;float:left}
		#mirrors li.row2{background:#E2EAEB}
		#mirrors li.placeholder{background:#FFE45C}
		#mirrors .right{float:right;margin-top:-2px}
		#mirrors .right a{cursor:pointer;font-weight:bold;text-decoration:none}
		#mirrors .right a:hover{color:#ff0000}

		#dlmpage fieldset label{font-weight:normal}
		#dlmpage input.error{border-color:#ff0000}
		#dlmpage label {display: block;margin-bottom: 5px;font-weight:bold}
		#dlmpage input ~ label {margin-top: 15px}
		#dlmpage fieldset legend {padding-bottom: 0}
		#dlmpage label ~ * {margin-left: 10px}

		#dlmpage fieldset{margin-bottom:1em}
		#dlmpage fieldset div{margin-bottom:1em}
		dl{margin-left:15px}
		dd{margin-left:15px}
		dt{margin-top:10px}
		'));
	}
?>