<?php
	if (!isset($gCms)) exit;
	ob_end_clean();ob_end_clean();

	if($params['dlmode'] == 'd' || !isset($params['dlmode'])) {
		$item_id = isset($params['item']) ? (int) $params['item'] : false;
		$dbitem = ($item_id !== false) ? $this->GetItem($item_id) : false;
	
		if($dbitem !== false && $dbitem['type'] == 1 && $dbitem['active'] == 1) {
			$download = $this->GetDownload($item_id);
			
			if(substr($download['location'], 0, 2) == '$$') {
				$location = substr($download['location'], 2);
				
				$info = FileInfo('$$'.$location);
				$identifier	= $info['identifier'];
				filename	= $info['filename'];
				$fileext	= $info['fileext'];
				
				$obfuscate		= (int) $this->GetPreference('obfuscate', 1);
				$ref_filtering	= (int) $this->GetPreference('referer', 1);
				
				$referer = trim(str_replace('www.', '', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
				
				$allowed = array('');
				switch($ref_filtering) {
					case 0:
						$allowed = true;
					break;
					case 2:
						$allowed = explode(';', $this->GetPreference('allowed_referer', '').';');
					break;
					default:
					case 1:
						$allowed = array(str_replace('www.', '', $_SERVER['HTTP_HOST']), '');
					break;
				}
				
				
				if($allowed === true || (is_array($allowed) && in_array($referer, $allowed))) {
					switch($obfuscate) {
						case 0: default:
							
						break;
						case 1:
							$tmpdir = cms_join_path('tmp', 'downloads');
							
							$srcfile = cms_join_path('downloads', substr($download['location'], 2));
							$tmpfile = cms_join_path($tmpdir, $identifier, $filename.$fileext);
							
							$desturl = $config['root_url'] . '/tmp/downloads/' . $identifier . '/' . $filename.$fileext;
							
							if(!file_exists($tmpfile)) {
								ScanTempDownloads();
								@mkdir(cms_join_path($tmpdir, $identifier));
								@chmod(cms_join_path($tmpdir, $identifier), 0777);
								if (copy($srcfile, $tmpfile)) {
									@chmod($tmpfile, 0777);
									
									DownloadCounter(&$this, $item_id);
									header("Location: ".$desturl);exit;
								} else {
									die($this->Lang('error'));
								}
							} else {
								touch($tmpfile);
								ScanTempDownloads();
								
								DownloadCounter(&$this, $item_id);
								header("Location: ".$desturl);exit;
							}
						break;
					}
				} else {
					header("HTTP/1.0 403 Forbidden");
					echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.$this->Lang('downloads').'</title></head><body><div>'.$this->Lang('error_forbidden').'</div></body></html>';exit;
				}		
			} else {
				DownloadCounter(&$this, $item_id);
				
				$desturl = $download['location'];
				header("Location: ".$desturl);exit;
			}
		} else {
			header("HTTP/1.0 404 Not Found");
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.$this->Lang('downloads').'</title></head><body><div>'.$this->Lang('error_notfound').'</div></body></html>';exit;
		}
	} elseif (substr($params['dlmode'], 0, 1) == 'm') {	
		$mirror_id = (int) substr($params['dlmode'], 1);
		$mirror = $this->GetMirror($mirror_id);
		
		DownloadCounter(&$this, $params['item'], $mirror_id);
		
		$desturl = $mirror['location'];
		
		header("Location: ".$desturl);exit;
	}	
?>