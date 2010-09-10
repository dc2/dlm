<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;
	$item_parent = (int) $return[1];

	if (isset($params['cancel'])) {
		if($return === false) {
			$params = array();
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			$params = array('tab_message' => 'download_added', 'item_id' => $return[1]);
			$this->Redirect($id, $return[0], '', $params);
		}
	}

	$item_name		= (isset($params['item_name'])		? $params['item_name']		: false);
	$item_desc		= (isset($params['item_desc'])		? $params['item_desc']		: false);
	$item_parent	= (isset($params['item_parent'])	? $params['item_parent']	: $item_parent);
	$item_location 	= (isset($params['item_location'])	? $params['item_location']	: false);

	$item_filesize	= (isset($params['item_filesize'])	? str_replace('.', '', $params['item_filesize']) : false);

	if (isset($params['submit'])) {

		$dldir = cms_join_path(dirname(__FILE__), '..', '..', 'downloads', '');

		if(!empty($_FILES[$id.'item_upload']['tmp_name'])) {
			if(is_dir($dldir) && is_writable($dldir)) {
				$tmp_name = $_FILES[$id.'item_upload']['tmp_name'];
				$filename = $_FILES[$id.'item_upload']['name'];
				$fileext = substr(strrchr($filename, '.'), 1);

				$md5 = md5_file($tmp_name);
				$new_filename = str_replace('.'.$fileext, '', $_FILES[$id.'item_upload']['name']) . '_' . md5($filename . $md5 . microtime()) . '.' . $fileext;
				$item_filesize = $_FILES[$id.'item_upload']['size'];

				if(ValidateExtension($this, $filename)) {
					if(!file_exists($dldir . $new_filename)) {
						if(!@move_uploaded_file($tmp_name, $dldir . $new_filename)) {
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
			if (!ValidateURL($item_location))  {
				$this->errors[] = $this->Lang('error_malformedurl');
				$item_location = false;
			} elseif(ValidateExtension($this, $item_location)) {
				$item_location = str_replace($config['root_url'] . '/downloads/', '$$', $item_location);
			} else {
				$this->errors[] = $this->Lang('error_fileext');
				$item_location = false;
			}
		}

		if ($item_name !== false && strlen(trim($item_name)) > 0) {
			if($item_location !== false && count($this->errors) == 0) {
				$node = $this->tree->InsertNode($item_parent, array('name' => $item_name, 'description' => $item_desc, 'type' => 1));
				if($node !== false) {
					$query = 'INSERT INTO '.cms_db_prefix().'module_dlm_downloads (dl_parent_id, location, size, downloads, created_date, modified_date) '."VALUES (?, ?, ?, 0, NOW(), NOW())";
					$result = $this->db->Execute($query, array($node, $item_location, $item_filesize));

					$this->Audit($node, $item_name, 'DLM: Download added');
					$this->SendEvent('DownloadAdded', array('dl_item' => array('id' => $node, 'name' => $item_name)));
					$this->tree->UpdateDownloadCount($node);

					if(isset($params['mirror_names']) && is_array($params['mirror_names'])) {
						$newmirrors = array_combine($params['mirror_names'], $params['mirror_urls']);
						$i = 0;

						foreach($newmirrors as $name => $url) {
							if(trim($name) != '') {
								if(ValidateURL($mirror_url)) {
									$query = 'INSERT INTO '.cms_db_prefix().'module_dlm_mirrors (dl_parent_id, position, name, location, downloads) VALUES (?, ?, ?, ?, 0)';
									$this->db->Execute($query, array($node, ++$i, $name, $url));
								}# else $this->errors[] = $this->Lang('error_malformedurl');
							}# else $this->errors[] = $this->Lang('error_noname');
						}
					}

					if($return === false) {
						$params = array('tab_message' => 'download_added', 'active_tab' => 'general');
						$this->Redirect($id, 'defaultadmin', '', $params);
					} else {
						$params = array('tab_message' => 'download_added', 'item_id' => $return[1]);
						$this->Redirect($id, $return[0], '', $params);
					}
				} else {
					$this->errors[] = $this->Lang('error_dbinsert');
				}
			} elseif($item_location == false && count($this->errors) == 0){
				$this->errors[] = $this->Lang('error_nofile');
			}
		} else {
			$this->errors[] = $this->Lang('error_nodownloadname');
		}
	}

	if(isset($params['edit_location'])) {
		if(substr($item_location, 0, strlen($config['root_url'])) == $config['root_url']) {
			if(!@unlink(str_replace('/', DIRECTORY_SEPARATOR, str_replace($config['root_url'], $config['root_path'], $item_location)))) {
				$this->errors[] = $this->Lang('error_delete');
			}
		}
		$item_location = false;
	} else {
		if(substr($item_location, 0, 2) == '$$') {
			$item_location = $config['root_url'] . '/downloads/' . substr($item_location, 2);
		}
	}

	$this->smarty->assign('headline', $this->Lang('add_download'));
	$this->smarty->assign('path_text', $this->Lang('path_text'));
	$this->smarty->assign('path', $this->GetPath($item_parent, $id, $returnid, 1, $this->Lang('add_download'), isset($params['return']) ? $params['return'] : false));

	$this->smarty->assign('name_text', $this->Lang('name'));
	$this->smarty->assign('name_value', $item_name);

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
		$this->smarty->assign('allowed_list', '.'.str_replace(';', ', .', $this->whitelist/*substr($this->whitelist, strlen($this->whitelist)-2) == ';' ? $this->whitelist : substr($this->whitelist, 0, strlen($this->whitelist))*/));
	} elseif($this->blacklist != '') {
		$this->smarty->assign('forbidden_text', $this->Lang('forbidden_extensions'));
		$this->smarty->assign('forbidden_list', '.'.str_replace(';', ', .', $this->blacklist/*substr($this->blacklist, strlen($this->blacklist)-2) == ';' ? $this->blacklist : substr($this->blacklist, 0, strlen($this->blacklist))*/));
	}

	$this->smarty->assign('location_text', $this->Lang('location'));
	$this->smarty->assign('location_value', $item_location);

	$this->smarty->assign('mirror_text', $this->Lang('mirror'));
	$this->smarty->assign('mirror_name', $this->Lang('name'));
	$this->smarty->assign('mirror_url', $this->Lang('url'));
	$this->smarty->assign('add_mirror', $this->Lang('add_mirror'));
	$this->smarty->assign('areyousure_mirror', $this->Lang('areyousure_mirror'));

	$this->smarty->assign('parent_text', $this->Lang('parent_category'));
	$this->smarty->assign('parent_input', $this->CreateInputDropdown($id, 'item_parent', $this->GetTreeInput(0), $item_parent));

	$this->smarty->assign('desc_text', $this->Lang('desc'));
	$this->smarty->assign('desc_value', $item_desc);

	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));

	echo $this->DisplayErrors();

	$this->smarty->assign('js_effects', $this->GetPreference('js_effects', 0));

	echo $this->CreateFormStart($id, 'add_download', $returnid, 'post', 'multipart/form-data');

	echo $this->ProcessTemplate('admin/common.js.tpl');
	echo $this->ProcessTemplate('admin/ajax.tpl');
	echo $this->ProcessTemplate('admin/edit_download.tpl');

	if($return !== false) echo $this->CreateInputHidden($id, 'return', implode(',', $return));
	echo $this->CreateFormEnd();
?>