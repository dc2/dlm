<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

	$this->theme =& $gCms->variables['admintheme'];

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	if (isset($params['cancel'])) {
		if($return === false) {
			$params = array();
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			$params = array('tab_message' => 'download_edited', 'item_id' => $return[1]);
			$this->Redirect($id, $return[0], '', $params);
		}
	}

	$item_id = isset($params['item_id']) ? $params['item_id'] : '';

	if ($item_id == '') {
		$this->Redirect($id, 'defaultadmin', $returnid);
	}

	$dbitem = $this->tree->GetItem($item_id);

	if(isset($params['ajax']) && $params['ajax'] === "true") {
		ob_start();
	}

	if($dbitem['type'] == 1) {
		$download = $this->GetDownload($item_id);

		$item_name		= (isset($params['item_name'])		? $params['item_name']		: false);
		$item_desc		= (isset($params['item_desc'])		? $params['item_desc']		: false);
		$item_parent	= (isset($params['item_parent'])	? $params['item_parent']	: false);
		$item_location	= (isset($params['item_location'])	? $params['item_location']	: false);
		$mirror_name	= (isset($params['mirror_name'])	? $params['mirror_name']	: false);
		$mirror_url		= (isset($params['mirror_url'])		? $params['mirror_url']		: false);

		$item_filesize	= (isset($params['item_filesize'])	? str_replace('.', '', $params['item_filesize']) : false);

		if (isset($params['submit']) || isset($params['temp']) || (isset($params['temp_2']) && $params['temp_2'] == 'true')) {
			if(!empty($_FILES[$id.'item_upload']['tmp_name']) && $_FILES[$id.'item_upload']['error'] == 0 && $_FILES[$id.'item_upload']['size'] > 0) {
				$dldir = cms_join_path(dirname(__FILE__), '..', '..', 'downloads', '');

				if(is_dir($dldir) && is_writable($dldir)) {
					$tmp_name = $_FILES[$id.'item_upload']['tmp_name'];
					$filename = $_FILES[$id.'item_upload']['name'];
					$fileext = substr(strrchr($filename, '.'), 1);

					$md5 = md5_file($tmp_name);
					$new_filename = str_replace('.'.$fileext, '', $_FILES[$id.'item_upload']['name']) . '_' . md5($filename . $md5 . $item_id) . '.' . $fileext;
					$item_filesize = $_FILES[$id.'item_upload']['size'];

					if(ValidateExtension($this, $filename)) {
						if(!file_exists($dldir . $new_filename)) {
							if(!move_uploaded_file($tmp_name, $dldir . $new_filename)) {
								$this->errors[] = $this->Lang('error_upload');
							} else {
								$item_location = '$$'.$new_filename;
							}
						} else {
							$time = microtime();
							if(move_uploaded_file($tmp_name, $dldir . md5($time)) &&	unlink($dldir . md5($time))) {
								$item_location = '$$'.$new_filename;
							} else {
								$this->errors[] = $this->Lang('error_filedelete');
							}
						}
					} else {
						$this->errors[] = $this->Lang('error_fileext');
						$item_location = false;
					}
				} else {
					$this->errors[] = $this->Lang('error_downloadsdir');
				}
			} elseif(!empty($params['item_location'])) {
				if (!ValidateURL($item_location)) {
					$this->errors[] = $this->Lang('error_malformedurl');
				} elseif(ValidateExtension($this, $item_location)) {
					$item_location = str_replace($config['root_url'] . '/downloads/', '$$', $item_location);
				} else {
					$this->errors[] = $this->Lang('error_fileext');
					$item_location = false;
				}
			} else {
				$this->errors[] = $this->Lang('error_nofile');
			}

			$item_location = count($this->errors) == 0 ? $item_location : false;

			if ($item_name !== false && $item_parent !== false && strlen(trim($item_name)) > 0) {
				if($dbitem['parent'] != $item_parent) {
					$this->tree->MoveNode($item_id, $item_parent, $dbitem['parent']);
				}

				if($item_location != false) {
					$query = 'UPDATE '.cms_db_prefix().'module_dlm_downloads SET location=?, size=?, modified_date=NOW() WHERE dl_parent_id = ?';
					$this->db->Execute($query, array($item_location, $item_filesize, $item_id));

					$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET name=?, description=?, parent=?, type=? WHERE dl_id = ?';
					$this->db->Execute($query, array($item_name, $item_desc, $item_parent, 1, $item_id));

					$this->Audit($item_id, $item_name, 'DLM: Download edited');
					$this->SendEvent('DownloadEdited', array('dl_item' => array('id' => $item_id, 'name' => $item_name)));
				}
			} else {
				$this->errors[] = $this->Lang('error_nodownloadname');
			}

			if(isset($params['mirror_names']) && is_array($params['mirror_names'])) {
				$i = 0;
				foreach($params['mirror_names'] as $mirror_name) {
					$mirror_url = current($params['mirror_urls']);
					$mirror_id = current($params['mirror_ids']);

					if(trim($mirror_name) != '') {
						if(ValidateURL($mirror_url)) {
							if($mirror_id == 'false') {
								$query 	= 'INSERT INTO '.cms_db_prefix().'module_dlm_mirrors (dl_parent_id, position, name, location, downloads) VALUES (?, ?, ?, ?, 0)';
								$this->db->Execute($query, array($item_id, ++$i, $mirror_name, $mirror_url));
							} elseif(substr($mirror_id, 0, 6) == 'delete') {
								$query 	= 'DELETE FROM '.cms_db_prefix().'module_dlm_mirrors WHERE dl_mirror_id = ?';
								$this->db->Execute($query, array((int)substr($mirror_id, 6)));
							} else {
								$query 	= 'UPDATE '.cms_db_prefix().'module_dlm_mirrors SET position = ?, name = ?, location = ? WHERE dl_mirror_id = ?';
								$this->db->Execute($query, array(++$i, $mirror_name, $mirror_url, $mirror_id));
							}
						}# else $this->errors[] = $this->Lang('error_malformedurl');
					}# else $this->errors[] = $this->Lang('error_noname');

					next($params['mirror_urls']);next($params['mirror_ids']);next($params['mirror_names']);
				}
			}

			if(isset($params['submit']) && count($this->errors) == 0) {
				if($return === false) {
					$params = array('tab_message' => 'download_updated', 'active_tab' => 'general');
					$this->Redirect($id, 'defaultadmin', '', $params);
				} else {
					$params = array('tab_message' => 'download_updated', 'item_id' => $return[1]);
					$this->Redirect($id, $return[0], '', $params);
				}
			} elseif(count($this->errors) == 0 && isset($params['temp'])) {
				echo $this->ShowMessage($this->Lang('download_updated'));
			}
		}

		$item_name 		= ($item_name		== false) ? $dbitem['name']			: $item_name;
		$item_desc 		= ($item_desc		== false) ? $dbitem['description']	: $item_desc;
		$item_parent 	= ($item_parent		== false) ? $dbitem['parent']		: $item_parent;
		$item_filesize	= ($item_filesize	== false) ? $download['size']		: $item_filesize;
		$item_location 	= ($item_location	== false) ? $download['location']	: $item_location;

		if(isset($params['edit_location'])) {
			if(substr($item_location, 0, strlen($config['root_url'])) == $config['root_url']) {
				$filename = str_replace($config['root_url'], '', $item_location);

				if(strpos($filename, '/downloads/') === 0) {
					$filename = str_replace('/', DIRECTORY_SEPARATOR, str_replace($config['root_url'], $config['root_path'], $item_location));
					if(!@unlink($filename)) {
						$this->errors[] = $this->Lang('error_delete');
					}
				}
			}
			$item_location = false;
		} else {
			if(substr($item_location, 0, 2) == '$$') {
				$item_location = $config['root_url'] . '/downloads/' . substr($item_location, 2);
			}
		}

		if(isset($params['ajax']) && $params['ajax'] === "true") {
			$content = ob_get_contents();ob_end_clean();
			if(count($this->errors) == 0) {
				echo "1,";
				echo $this->Lang('download_updated');
			} else {
				echo "0,";
				echo $this->DisplayErrors(true);
			}
			exit;
		}

		echo $this->DisplayErrors();

		$this->smarty->assign('js_effects', $this->GetPreference('js_effects', 0));

		$this->smarty->assign('headline', $this->Lang('edit_download'));
		$this->smarty->assign('path_text', $this->Lang('path_text'));
		$this->smarty->assign('path', $this->GetPath($item_id, $id, $returnid, 1, false, "edit_download,$item_id"));

		$this->smarty->assign('name_text', $this->Lang('name'));
		$this->smarty->assign('name_value', html_entity_decode($item_name, ENT_QUOTES, 'UTF-8'));

		$this->smarty->assign('filesize_text', $this->Lang('filesize'));
		$this->smarty->assign('filesize_value', (int)$item_filesize);

		if(empty($item_location)) {
			$this->smarty->assign('upload_text', $this->Lang('upload'));

			$this->smarty->assign('or', $this->Lang('or'));
		} else {
			$this->smarty->assign('edit_location', $this->CreateInputSubmit($id, 'edit_location', $this->Lang('edit_location')));
		}

		$this->LoadBwList();
		if($this->whitelist != '') {
			$this->smarty->assign('allowed_text', $this->Lang('allowed_extensions'));
			$this->smarty->assign('allowed_list', '.'.str_replace(';', ', .', $this->whitelist));
		} elseif($this->blacklist != '') {
			$this->smarty->assign('forbidden_text', $this->Lang('forbidden_extensions'));
			$this->smarty->assign('forbidden_list', '.'.str_replace(';', ', .', $this->blacklist));
		}

		$this->smarty->assign('location_text', $this->Lang('location'));
		$this->smarty->assign('location_value', $item_location);

		$this->smarty->assign('mirror_text', $this->Lang('mirror'));
		#$this->smarty->assign('mirror_desc', $this->Lang('mirror_desc'));
		$this->smarty->assign('mirror_name', $this->Lang('name'));
		$this->smarty->assign('mirror_url', $this->Lang('url'));
		$this->smarty->assign('add_mirror', $this->Lang('add_mirror'));
		$this->smarty->assign('areyousure_mirror', $this->Lang('areyousure_mirror'));

		$this->smarty->assign('mirrors', $this->GetMirrors($item_id, $id, $returnid, true));

		$this->smarty->assign('parent_text', $this->Lang('parent_category'));
		$this->smarty->assign('parent_input', $this->CreateInputDropdown($id, 'item_parent', $this->GetTreeInput(0, $item_id), $item_parent));

		$this->smarty->assign('desc_text', $this->Lang('desc'));
		$this->smarty->assign('desc_value', htmlspecialchars($item_desc));

		$this->smarty->assign('ajax', $this->CreateInputHidden($id, 'ajax', 'false'));

		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
		$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
		$this->smarty->assign('temp', $this->CreateInputSubmit($id, 'temp', $this->Lang('savetemp')));

		$this->smarty->assign('startform', $this->CreateFormStart($id, 'edit_download', $returnid, 'post', 'multipart/form-data'));
		$this->smarty->assign('endform', $this->CreateFormEnd());

		$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'item_id', $item_id) . ($return !== false ? $this->CreateInputHidden($id, 'return', implode(',', $return)) : ''));

		$this->smarty->assign('toggle', $this->Lang('toggle'));

		echo $this->ProcessTemplate('admin/common.js.tpl');
		echo $this->ProcessTemplate('admin/ajax.tpl');
		echo $this->ProcessTemplate('admin/mirrorlist.tpl');
		echo $this->ProcessTemplate('admin/edit_download.tpl');
	} else {
		$params = array('active_tab' => 'general', 'tab_message' => 'error_nodownload');
		$this->Redirect($id, 'defaultadmin', '', $params);
	}
?>