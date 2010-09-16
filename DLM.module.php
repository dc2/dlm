<?php
define('TPL_SEPARATOR', '<!-- // :::TPL-SEPARATOR::: // -->');

error_reporting(E_ALL|E_STRICT|E_NOTICE);
#error_reporting(E_ALL);

class DLM extends CMSModule {
	var $db;
	var $tree;
	var $theme;

	public $errors = array();

	var $table = 'module_dlm_items';
	var $prefix = 'dl';

	var $blacklist = false;
	var $whitelist = false;

	function DLM() {
		parent::CMSModule();

		$this->db =& $this->GetDb();

		require_once(cms_join_path(dirname(__FILE__), 'misc.functions.php'));
		require_once(cms_join_path(dirname(__FILE__), 'libs', 'dltree.class.php'));

		$this->tree = new dltree(cms_db_prefix().$this->table, $this->prefix, $this->db);
	}

	function GetName() {
		return 'DLM';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.8';
	}

	function GetHelp() {
		return $this->Lang('help');
	}

	function GetAuthor() {
		return 'dc2';
	}

	function GetAuthorEmail() {
		return 'dc2@worldofanno.de';
	}

	function GetChangeLog() {
		return $this->Lang('changelog');
	}

	function AdminStyle() {
		return GetAdminStyle();
	}

	function IsPluginModule() {
		return true;
	}

	function HasAdmin() {
		return true;
	}

	function GetAdminSection() {
		return 'content';
	}

	function GetAdminDescription() {
		return $this->Lang('moddescription');
	}

	function VisibleToAdminUser() {
		return $this->CheckPermission('Use DLM');
	}

	function GetDependencies() {
		return array();
	}

	function MinimumCMSVersion() {
		return "1.6.7";
	}

	function SetParameters() {
		$returnid = $this->GetPreference('returnid', false);

		$this->RestrictUnknownParams();
		$defaults = array('action'=>'default', 'inline' => true, 'returnid' => $returnid);

		$this->RegisterRoute('/dlm\/(?P<item>[0-9]+)(?P<dlmode>[dm]+[0-9]*)\/(?P<junk>[0-9a-zA-Z\-]+?)$/', array('action' => 'download', 'suppressoutput' => true));

		$this->RegisterRoute('/dlm\/(?P<item>[0-9]+)\/$/', $defaults);
		$this->RegisterRoute('/dlm\/(?P<item>[0-9]+)\/(?P<junk>.*)$/', $defaults);

		// syntax for creating a parameter is parameter name, default value, description
		$this->CreateParameter('item', 0, $this->Lang('help-item'));
		$this->SetParameterType('item', CLEAN_INT);

		$this->CreateParameter('template', 'default.tpl', $this->Lang('help-template'));
		$this->SetParameterType('template', CLEAN_STRING);

		$this->CreateParameter('root', 0, $this->Lang('help-root'));
		$this->SetParameterType('root', CLEAN_INT);

		$this->CreateParameter('dlmode', 'default', $this->Lang('help-dlmode'));
		$this->SetParameterType('dlmode', CLEAN_STRING);

		$this->CreateParameter('showpath', true, $this->Lang('help-showpath'));
		$this->SetParameterType('showpath', CLEAN_NONE);

		$this->CreateParameter('showmirror', true, $this->Lang('help-showmirror'));
		$this->SetParameterType('showmirror', CLEAN_NONE);

		$this->CreateParameter('showdesc', true, $this->Lang('help-showdesc'));
		$this->SetParameterType('showdesc', CLEAN_NONE);

		$this->SetParameterType('junk', CLEAN_STRING);
	}

	function GetEventDescription($eventname)
	{
		return $this->lang('evd-' . $eventname);
	}

	function GetEventHelp($eventname)
	{
		return $this->lang('evd-' . $eventname);
	}

	function InstallPostMessage() {
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage() {
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage() {
		return $this->Lang('really_uninstall');
	}

	# overloaded
	function ListTemplates($id = false, $returnid = false, $return = false) {
		$tpls = array();
		$default_tpl = $this->GetDefaultTemplate();

		$query = 'SELECT template_name AS name from '.cms_db_prefix().'module_templates WHERE module_name = ? ORDER BY template_name ASC';
		$result = $this->db->Execute($query, array('DLM'));

		while ($tpl = $result->FetchRow()) {
			$name = $this->CreateLink($id, 'edit_template', $returnid, $tpl['name'], array('tpl_name'=>$tpl['name'], 'return' => $return));
			$edit = $this->CreateLink($id, 'edit_template', $returnid, $this->theme->DisplayImage('icons/system/edit.gif', $this->Lang('edit_template'),'','','systemicon'), array('tpl_name'=>$tpl['name'], 'return' => $return));
			$delete = $this->CreateHandlerLink($id, 'delete_template', $returnid, $this->theme->DisplayImage('icons/system/delete.gif', $this->Lang('delete_template'),'','','systemicon'), array('tpl_name'=>$tpl['name'], 'return' => $return),'', false,false, 'class="deletetpl"');

			$default = $this->CreateHandlerLink($id, 'set_default_template', $returnid, ($default_tpl == $tpl['name']) ? $this->theme->DisplayImage('icons/system/true.gif', $this->Lang('default'),'','','systemicon') : $this->theme->DisplayImage('icons/system/false.gif', $this->Lang('default'),'','','systemicon'), array('tpl_name'=>$tpl['name'], 'return' => $return), '', false, false, 'class="defaulttpl"');

			$tpls[] = array('edit' => $edit, 'delete' => $delete, 'name' => $name, 'default' => $default);
		}

		$files = ListDir(cms_join_path(dirname(__FILE__), 'templates', ''), '.tpl');

		foreach($files as $file) {
			$import = $this->CreateLink($id, 'edit_template', $returnid, $this->theme->DisplayImage('icons/system/import.gif', $this->Lang('import_template'),'','','systemicon'), array('tpl_name'=>$file, 'tpl_import' => true, 'return' => $return));
			$default = $this->CreateHandlerLink($id, 'set_default_template', $returnid, ($default_tpl == $file) ? $this->theme->DisplayImage('icons/system/true.gif', $this->Lang('default'),'','','systemicon') : $this->theme->DisplayImage('icons/system/false.gif', $this->Lang('default'),'','','systemicon'), array('tpl_name'=>$file, 'return' => $return), '', false, false, 'class="defaulttpl"');

			$tpls[] = array('import' => $import, 'name' => $file, 'default' => $default);
		}

		return $tpls;
	}

	function GetDefaultTemplate(){
		$query = 'SELECT description FROM '.cms_db_prefix().'module_dlm_items WHERE dl_id = ?';

		$result = $this->db->Execute($query, array(0));
		$default_tpl = $result->FetchRow();

		return $default_tpl['description'];
	}

	# overloaded
	function GetTemplate($tpl){
		if(substr($tpl, -4) != '.tpl')
			$content = parent::GetTemplate($tpl);
		else {
			$content = file_get_contents(cms_join_path(dirname(__FILE__), 'templates', $tpl));
		}

		return $content;
	}

	function LoadTemplate($tpl, $separator = TPL_SEPARATOR){
		$content = trim($this->GetTemplate($tpl));

		if(strrpos($tpl, '.tpl') === 0 || $content === '') {
			$file = ($content == '') ? $this->GetDefaultTemplate() : $tpl;
			$content = trim($this->GetTemplate($file));
		}

		return SplitTemplate($content, $separator);
	}

	# overloaded
	function CreateInputDropdown($id, $name, $items, $selected = false, $addtext = '') {
		$text = '<select class="cms_dropdown" id="' . $id . $name . '" name="' . $id . $name . '"'. ($addtext != '' ? ' ' . $addtext : '') . '>';

		if (is_array($items) && count($items) > 0) {
			foreach ($items as $key => $value) {
				$text .= '<option value="' . $key . '"' . ($selected == $key ? ' ' . 'selected="selected"' : '') . '>' . $value . '</option>';
			}
		}

		return $text . '</select>' . "\n";
	}

	/* Methods for search-support */
	function SearchResult($returnid, $node, $attr = '') {
		$result = array();

		$query = "SELECT name FROM ". cms_db_prefix()."module_dlm_items WHERE dl_id = ?";
		$result = $this->db->Execute($query, array($node));
		if ($result) {
			$row = $result->FetchRow();

			//0 position is the prefix displayed in the list results.
			$searchresult[0] = $this->Lang('downloads').': '.$row['name'];

			//1 position is the title
			$searchresult[1] = $row['name'];

			//2 position is the URL to the title.
			$prettyurl = MakePretty($node, $returnid, $row['name']);
			$searchresult[2] = $this->CreateLink('cntnt01', 'default', $returnid, $row['name'], array('item'=>$node), '', true, false, '', false, $prettyurl);
		}

		return $searchresult;
	}

	function SearchReindex(&$module) {
		$query  = 'SELECT * FROM ' . cms_db_prefix() . 'module_dlm_items ORDER BY dl_left';
		$result = $this->db->Execute($query);

		while ($row = $result->FetchRow()) {
			if ($row['active'] == 1) {
				$module->AddWords($this->GetName(), $row['dl_id'], $row['type'] == 1 ? 'download' : 'category', $row['name'] . ' ' . $row['description']);
			}
		}
	}

	function AjaxResponse($success = '', $error = false, $display_errors = true){
		global $params;

		$error = $error == false ? $this->DisplayErrors(true) : $error;

		if(isset($params['ajax']) && $params['ajax'] == "true") {
			ob_end_clean();
			if(count($this->errors) == 0) {
				echo '1,'.$success;
			} else {
				echo '0,';
				echo $error;
			}
			exit;
		} elseif($display_errors === true)
			echo $this->DisplayErrors();
	}

	function CreateHandlerLink($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false, $prettyurl='') {
		$params['_action'] = $action;
		$action = 'actionhandler';
		return parent::CreateLink($id, $action, $returnid, $contents, $params, $warn_message, $onlyhref, $inline, $addttext, $targetcontentonly, $prettyurl);
	}

	function DisplayErrors($mode = false) {
		$result = '';

		if($mode === false) {
			if(count($this->errors) > 0)
				$result = $this->ShowErrors($this->errors);
		} else {
			$result = '<ul class="pageerror">';
			foreach($this->errors as $error) {
				$result .= trim($error) != '' ? '<li>'.$error.'</li>' : '';
			}
			$result .= '</ul>';
		}

		return $result;
	}

	function LoadBwList() {
		$this->blacklist = trim(($this->blacklist !== false) ? $this->blacklist : $this->GetPreference('blacklist', false));
		$this->whitelist = trim(($this->whitelist !== false) ? $this->whitelist : $this->GetPreference('whitelist', false));
	}

	function MoveNode($node, $newparent, $oldparent, $condition = ''){
		$this->tree->MoveAll($node, $newparent, $condition);
		$this->tree->RecalcDownloadsAll(array($newparent, $oldparent));

		$this->SendEvent('ItemMoved', array('dl_item' => array('id' => $node)));
	}

	function DeleteBranch($node) {
		$this->tree->DeleteDownloads($node);
		$this->tree->DeleteAll($node);

		$this->SendEvent('ItemDeleted', array('dl_item' => array('id' => $node)));

		return true;
	}

	function GetTreeAdmin($node, $id, $indent = 0, &$dbtree = NULL, $return = false) {
		#$this->tree->RebuildTree(0, 1, true);
		$items = array();
		$oldlevel = 0;
		$skipuntil = false;

		$returnid = NULL;

		$dbtree = $dbtree === NULL ? $this->tree->GetItemsDB($node, array('dl_id', 'dl_left', 'dl_right', 'dl_level', 'expand', 'active', 'parent', 'type', 'name')) : $dbtree;
		$count = count($dbtree);

		if($count > 1) {
			$root = reset($dbtree);
			$last_right = $root['dl_right'];
			$parent_right = $last_right;

			$root_level = $root['dl_level'];

			while ($row = next($dbtree)) {
				$key = key($dbtree);
				if($key != $node){
					if($skipuntil === false || $row['dl_left'] > $skipuntil) {
						$skipuntil = ($row['expand'] == 0) ? $row['dl_right'] : false;

						$onerow = new stdClass();
						$parent_info =& $dbtree[$row['parent']];

						$value 	= $row['name'];
						$level 	= $row['dl_level'];
						$type 	= $row['type'];

						$children = ($row['dl_right'] - $row['dl_left'] - 1) / 2;

						$prefix = ($type == 0) ? '&#8680;' : '&#9658;';
						$typetext = ($type == 0) ? 'category' : 'download';
						$typeimg = DisplayImage($typetext . '.png', $prefix, $this->Lang($typetext));

						$depthmarking = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $row['dl_level']-1-$indent) . $typeimg . '&nbsp;';

						$onerow->id = $key;
						$onerow->level = $level;
						$onerow->name = $depthmarking . $this->CreateLink($id, 'edit_'.$typetext, $returnid, $value, array('item_id'=>$key, 'return' => $return));

						$expand = ($row['expand'] == 0) ? 'expand' : 'contract';

						$onerow->expandlink = ($children > 0) ? $this->CreateHandlerLink($id, 'expand', $returnid, $this->theme->DisplayImage("icons/system/".$expand.".gif", lang($expand),'','','systemicon'), array('item_id' => $key, 'indent' => $indent, 'return' => $return, 'expand' => (($expand == 'expand') ? '1' : '0')), '', false, false, 'class="expand"') : false;
						$onerow->activatelink = $this->CreateHandlerLink($id, 'activate', $returnid, ($row['active'] == 1) ? $this->theme->DisplayImage('icons/system/true.gif', $this->Lang('deactivate'),'','','systemicon') : $this->theme->DisplayImage('icons/system/false.gif', $this->Lang('activate'),'','','systemicon'), array('item_id'=>$key, 'return' => $return), '', false, false, 'class="activate"');

						$onerow->editlink = $this->CreateLink($id, 'edit_'.$typetext, $returnid, $this->theme->DisplayImage('icons/system/edit.gif', $this->Lang('edit_'.$typetext),'','','systemicon'), array('item_id'=>$key, 'return' => $return));
						$onerow->deletelink = $this->CreateHandlerLink($id, 'delete', $returnid, $this->theme->DisplayImage('icons/system/delete.gif', $this->Lang('delete_item'),'','','systemicon'), array('item_id' => $key, 'return' => $return), ''/*$this->Lang('areyousure_item')*/, false, false, 'class="delete"');

						if ($oldlevel < $level) {
							$parent_right = $last_right;
						} elseif($oldlevel > $level) {
							$parent_right = $parent_info['dl_right'];
						} elseif ($oldlevel === $level) {
							$parent_right = $parent_info['dl_right'];
						} else {
							$parent_right = $root['dl_right'];
						}

						$itemcount = count($items);

						$nextlevel = next($dbtree);
						$nextlevel = $nextlevel['dl_level'];
						prev($dbtree);

						if ($itemcount < $count-2 && ($nextlevel === $level || ($nextlevel >= $level && ($row['dl_right'] < $parent_right-1)))) {
							$onerow->downlink = $this->CreateHandlerLink($id, 'move', $returnid, $this->theme->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon'), array('item_id' => $key, 'direction'=>'down', 'return' => $return), '', false, false, 'class="movelink" rel="down"');
						} else {
							$onerow->downlink = '';
						}

						if($itemcount > 0 && ($oldlevel === $level || $oldlevel > $level)) {
							$onerow->uplink = $this->CreateHandlerLink($id, 'move', $returnid, $this->theme->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon'), array('item_id' => $key, 'direction'=>'up', 'return' => $return), '', false, false, 'class="movelink"'.($onerow->downlink == '' ? ' style="margin-left: 20px"' : '').' rel="up"');
						} else {
							$onerow->uplink = '';
						}

						$oldlevel = $level;
						$last_right = $row['dl_right'];

						$items[$key] = $onerow;
					}
				}
			}
		}
		return $items;
	}

	function GetTree($node, $id = false, $returnid = false, &$dbtree = NULL){
		$items = array();
		$skipuntil = false;

		$dbtree = $dbtree === NULL ? $this->tree->GetItemsDB($node, array('dl_id', 'dl_left', 'dl_right', 'dl_level', 'active', 'name', 'type', 'downloads')) : $dbtree;
		$count = count($dbtree);

		if($count > 1) {
			$root = reset($dbtree);
			$root_level = $root['dl_level'];

			while ($row = next($dbtree)) {
				$key = key($dbtree);
				if($key != $node) {
					if($skipuntil === false || $row['dl_left'] > $skipuntil) {
						$skipuntil = ($row['active'] == 0 || ($row['type'] == 0 && $row['downloads'] == 0)) ? $row['dl_right'] : false;

						if(($row['type'] == 1 && $row['dl_level'] == $root_level + 1) || ($row['type'] == 0 && $row['downloads'] > 0) && $skipuntil === false) {
							$onerow = new stdClass();

							$name 	= $row['name'];
							$level 	= $row['dl_level'];
							$type 	= $row['type'];

							$prettyurl = MakePretty($key, $returnid, $name);


							$prefix = ($type == 0) ? '&#8680;' : '&#9658;';
							$typetext = ($type == 0) ? 'category' : 'download';

							$typeimg = DisplayImage($typetext . '.png', $prefix);

							$depthmarking = (($row['dl_level'] > ($root_level + 1)) ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $row['dl_level']-($root_level+1)) : '') . $typeimg . '&nbsp;';

							$onerow->id = $key;
							$onerow->name = $depthmarking . $this->CreateLink($id, 'default', $returnid, $name, array('item'=>$key), '', false, true, '', false, $prettyurl);
							$onerow->downloads = ($type == 0) ? $row['downloads'] : '';
							$onerow->downloadurl = ($type != 0) ? $this->CreateLink($id, 'download', $returnid, $name, array('item'=>$key), '', true, true, '', false, MakePretty($key.'d', false, $name)) : false;

							$items[$key] = $onerow;
						}
					}
				}
			}
			return $items;
		} else return false;
	}

	function GetTreeInput($node, $ignore = false) {
		$fields = array('dl_id', 'dl_left', 'dl_right', 'dl_level', 'parent', 'name');
		$condition = array('and' => array('type = 0'));
		$nodeinfo = $this->tree->GetNodeInfo($ignore);
		if($ignore !== false) {
			$nodeinfo = $this->tree->GetNodeInfo($ignore);
			$dbtree = $this->tree->GetItemsDB($node, $fields, array_merge(array('id' => $ignore), $nodeinfo), $condition);
		} else {
			$dbtree = $this->tree->GetItemsDB($node, $fields, false, $condition);
		}

		$output = array();
		$count = count($dbtree);

		$output[0] = $this->Lang('no_default');
		if($count > 0) {
			foreach ($dbtree as $key => $row) {
				if($key > 0) {
					$prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', (int) $row['dl_level']). '&#8680;&nbsp;';
					$output[$key] = $prefix.$row['name'];
				}
			}
		}
		return $output;
	}

	function GetPath($item_id, $id, $returnid, $root_info = false, $addtext = false, $return = false) {
		$this->tree->Parents($item_id, array('dl_id', 'name', 'type'), array('and' => array('dl_left >= '.(int)$root_info[0])));
		$path	= '';
		$title	= '';
		$root = (int)$root_info[3];

		$firstrow = $this->tree->NextRow();
		if($firstrow !== false) {
			while ($row = $this->tree->NextRow()) {
				$key = (int)$row['dl_id'];

				if($key != 0) {
					$value = $row['name'];
					$type = $row['type'];

					$prefix = ($type == 0) ? '&#8680;' : '&#9658;';
					$typetext = ($type == 0) ? 'category' : 'download';

					$typeimg = DisplayImage($typetext . '.png', $prefix, $this->Lang($typetext));

					if($return !== false) {
						$path .= '&nbsp;&nbsp;&#187;&nbsp;' . $typeimg . '&nbsp;' . (($key != $item_id || $addtext !== false) ? $this->CreateLink($id, 'edit_'.$typetext, $returnid, $value, array('item_id'=>$key, 'return' => $return)) : $value);
					} else {
						$prettyurl = MakePretty($key, $returnid, $value);
						$path .= '&nbsp;&nbsp;&#187;&nbsp;' . $typeimg . '&nbsp;' . (($key != $item_id || $addtext !== false) ? $this->CreateLink($id, 'default', $returnid, $value, array('item'=>$key), '', false, true, '', false, $prettyurl) : $value);
					}

					$title .= ($key != $item_id || $addtext !== false) ? $value .  '&nbsp;&nbsp;&#187;&nbsp;' : '';
				}
			}

			if(strlen($path) > 0 || $addtext !== false) {
				$value = ($firstrow['dl_id'] === 0 || $firstrow['name'] == 'root') ? $this->Lang('overview') : $firstrow['name'];
				$path = $this->CreateLink($id, 'default'.(($return !== false) ? 'admin' : ''), $returnid, $value, array('item'=>$root), '', false, true, 'rel="nofollow"', false, ($return === false) ? MakePretty($root, $returnid, $value) : false) . $path;
				$title = ($return !== false) ? $value .'&nbsp;&nbsp;&#187;&nbsp;' . $title : $title;
			}

			$path .= ($addtext !== false) ? '&nbsp;&nbsp;&#187;&nbsp;'.$addtext : '';
			$title	.= ($addtext !== false) ? '&nbsp;&nbsp;&#187;&nbsp;'.$addtext : '';
		}


		return array($path, ' - '.$title);
	}

	function GetDownload($item_id) {
		return $this->tree->GetDownload($item_id); // sourced out into dltree because it's needed there, too
	}

	function UploadFile($id){
		if(!empty($_FILES[$id.'item_upload']['tmp_name']) && $_FILES[$id.'item_upload']['error'] == 0 && $_FILES[$id.'item_upload']['size'] > 0) {
			$dldir = cms_join_path(dirname(__FILE__), '..', '..', 'downloads', '');

			if(is_dir($dldir) && is_writable($dldir)) {
				$tmp_name = $_FILES[$id.'item_upload']['tmp_name'];
				$filename = $_FILES[$id.'item_upload']['name'];
				$fileext = substr(strrchr($filename, '.'), 1);

				$md5 = md5_file($tmp_name);
				$new_filename = str_replace('.'.$fileext, '', $_FILES[$id.'item_upload']['name']) . '_' . md5($filename . $md5) . '.' . $fileext;
				$item_filesize = $_FILES[$id.'item_upload']['size'];

				if(ValidateExtension($this, $filename)) {
					if(!file_exists($dldir . $new_filename)) {
						if(!@move_uploaded_file($tmp_name, $dldir . $new_filename)) {
							$this->errors[] = $this->Lang('error_upload');
						} else {
							return '$$'.$new_filename;
						}
					} else {/* same file already exists - don't do anything */}
				} else {
					$this->errors[] = $this->Lang('error_fileext');
				}
			} else {
				$this->errors[] = $this->Lang('error_downloadsdir');
			}

			return false;
		} else return false;
	}

	/* Download-Counter (Frontend) */
	function DownloadCounter($item_id, $mirror_id = false) {
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_downloads SET downloads = downloads + 1 WHERE dl_parent_id = ?';
		$result = $this->db->Execute($query, array((int) $item_id));

		if($mirror_id !== false) {
			$query = 'UPDATE '.cms_db_prefix().'module_dlm_mirrors SET downloads = downloads + 1 WHERE dl_mirror_id = ?';
			$result = $this->db->Execute($query, array((int) $mirror_id));
		}
	}

	function GetMirror($item_id) {
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_mirrors WHERE dl_mirror_id = ?';
		$result = $this->db->Execute($query, array((int)$item_id));

		if($result->NumRows() > 0)
			return $result->FetchRow();
		else
			return false;
	}

	function GetMirrors($node, $id = false, $returnid = false, $admin = false, $size = 0) {
		$query = 'SELECT dl_mirror_id as ID, position, name, location, downloads FROM '.cms_db_prefix().'module_dlm_mirrors WHERE dl_parent_id = ? ORDER BY position ASC';
		$result = $this->db->Execute($query, array($node));

		if($result->NumRows() > 0) {
			if($admin === true) {
				$rows = $result->GetArray();
			} else {
				while($row = $result->FetchRow()) {
					$row['traffic'] = FormatFilesize((int)$size * (int)$row['downloads']);
					$rows[$row['ID']] = $row;
				}
			}
		} else return false;

		return $rows;
	}

	function UpdateMirrors($node, $mirror_names, $mirror_urls, $mirror_ids){
		$i = 0;
		foreach($mirror_names as $mirror_name) {
			$mirror_url = current($mirror_urls);
			$mirror_id = current($mirror_ids);

			if(trim($mirror_name) != '') {
				if(ValidateURL($mirror_url)) {
					if($mirror_id == 'false') {
						$query 	= 'INSERT INTO '.cms_db_prefix().'module_dlm_mirrors (dl_parent_id, position, name, location, downloads) VALUES (?, ?, ?, ?, 0)';
						$this->db->Execute($query, array($node, ++$i, $mirror_name, $mirror_url));
					} elseif(substr($mirror_id, 0, 6) == 'delete') {
						$query 	= 'DELETE FROM '.cms_db_prefix().'module_dlm_mirrors WHERE dl_mirror_id = ?';
						$this->db->Execute($query, array((int)substr($mirror_id, 6)));
					} else {
						$query 	= 'UPDATE '.cms_db_prefix().'module_dlm_mirrors SET position = ?, name = ?, location = ? WHERE dl_mirror_id = ?';
						$this->db->Execute($query, array(++$i, $mirror_name, $mirror_url, $mirror_id));
					}
				}# else $this->errors[] = $this->Lang('error_malformedurl');
			}# else $this->errors[] = $this->Lang('error_noname');

			next($mirror_names);next($mirror_urls);next($mirror_ids);
		}
	}

	/*function GetTemplate($tpl_name) {
		$query = 'SELECT * FROM '.cms_db_prefix().'module_templates WHERE t = ?';
		$result = $this->db->Execute($query, array((int)$item_id));

		if($result->NumRows() > 0)
			return $result->FetchRow();
		else
			return false;
	}*/
}
?>