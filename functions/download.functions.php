<?php
	if (!isset($gCms)) exit;
	
	/* Download-Counter (Frontend) */
	function DownloadCounter(&$dlm, $item_id, $mirror_id = false) {
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_downloads SET downloads = downloads + 1 WHERE dl_parent_id = ?';
		$result = $dlm->db->Execute($query, array((int) $item_id));
		
		if($mirror_id !== false) {
			$query = 'UPDATE '.cms_db_prefix().'module_dlm_mirrors SET downloads = downloads + 1 WHERE dl_mirror_id = ?';
			$result = $dlm->db->Execute($query, array((int) $mirror_id));
		}
	}
	
	function ScanTempDownloads($maxage = 43200 /* = 12h */, $currentdir = false, $level = 0) {
		$tmpdir = cms_join_path(dirname(__FILE__), '..', 'tmp', 'downloads');
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
						unlink($file);
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
	
	function readfile_chunked($filename,$retbytes=true) {
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		// $handle = fopen($filename, 'rb');
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
?>