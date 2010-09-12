{capture assign="itemlist"}
	{if $itemcount > 0}
		{*<script src="../modules/DLM/libs/js/jquery.form.min.js" type="text/javascript"></script>*}


		<script type="text/javascript">
		<!--
			var idRegExp = "m1_item_id=([0-9]+)";
			var levelRegExp = "level([0-9]+)";
			var selcount = 0;
			var $selcountspan = null;
			var rowcount = {$itemcount};
			var rootlevel = {$rootlevel} + 1;
			var unselectChildren = -1;

			{literal}
			function setupEvents($context) {
				jQuery('a.expand', $context).unbind().click(ExpandItem);
				jQuery('a.delete', $context).unbind().click(DeleteItem);
				jQuery('a.movelink', $context).unbind().click(MoveItem);
				jQuery('a.activate', $context).unbind().click(ActivateItem);
			}

			jQuery(function() {
				$selcountspan = jQuery('#selcount');

				setupEvents();
				updateListactions();

				jQuery.ajaxSetup({
					type: 'GET',
					beforeSend: function() {
						jQuery(document.body).addClass('wait');
					},
					complete: function () {
						jQuery(document.body).removeClass('wait');
					},
				});

				jQuery('#m1_moduleform_1').append('<input name="suppressoutput" id="suppressoutput" value="true" type="hidden" /><input name="m1_ajax" id="m1_ajax" value="true" type="hidden" />');
				jQuery('#m1_listsubmit').click(SubmitList);
			});

			// this function returns all children of the specified node in the DOM
			function getRowChildren($row, level) {
				var level = (typeof level != 'undefined') ? level : parseInt($row.attr('class').match(levelRegExp)[1]);
				var $children = $row.nextUntil('tr.level'+level);
				return $children.filter(function() {return (parseInt(jQuery(this).attr('class').match(levelRegExp)[1]) > level) ? true : false;});
			}

			/*function getRealSiblings($row, level, next) {
				var level = (typeof level != 'undefined') ? level : parseInt($row.attr('class').match(levelRegExp)[1]);

				var $siblings = (next == true) ? $row.nextAll() : $row.prevAll();

				$siblings.each(function(index) {
					if(jQuery(this).attr('class').match(levelRegExp)[1] < level) {
						$siblings = $siblings.slice(0, index);
						return false;
					}
				});

				return $siblings.filter('.level'+level);
			}*/

			/*
			function getRealSiblings($row, filter, level) {
				var level = (typeof level != 'undefined') ? level : parseInt($row.attr('class').match(levelRegExp)[1]);
				var $siblings = $row.nextUntil('tr.level'+(level-1)).filter('tr.level'+level).add($row.prevUntil('tr.level'+(level-1)).filter('tr.level'+level));

				if(typeof filter != 'undefined') {
					return $siblings.filter(filter);
				} else return $siblings;
			}
			*/

			function onDelete($el, id, level) {
				var $movelinks = $el.children('td.movelinks');

				// if there are two movelinks, no other row will be affected
				if($movelinks.children().length < 2) {
					// retrieve "real" siblings from DOM (siblings that are on the same level as node)
					var $nextAll = $el.nextUntil('tr.level'+(level-1)).filter('tr.level'+level);
					var $prevAll = $el.prevUntil('tr.level'+(level-1)).filter('tr.level'+level);

					$next = $nextAll.eq(0);
					$prev = $prevAll.eq(0);

					if($prevAll.length == 1 && $nextAll.length == 0) {			// if there is just one sibling left, delete the movelinks
						$prev.children('td.movelinks').empty();
					} else if($prevAll.length == 0 && $nextAll.length == 1) {
						$next.children('td.movelinks').empty();					// otherwise switch movelinks of next / previous sibling
					} else {
						if($next.length == 0) {
							if($prev.length != 0) {
								var previd = parseInt($prev.find('a.movelink').eq(0).attr('href').match(idRegExp)[1]);

								$prev.children('td.movelinks').replaceWith($movelinks.clone(true));
								$prev.children('td.movelinks').html(str_replace('m1_item_id='+id, 'm1_item_id='+previd, $prev.children('td.movelinks').html()));
							}
						} else if($prev.length == 0) {
							if($next.length != 0) {
								var nextid = parseInt($next.find('a.movelink').eq(0).attr('href').match(idRegExp)[1]);

								$next.children('td.movelinks').replaceWith($movelinks.clone(true));
								$next.children('td.movelinks').html(str_replace('m1_item_id='+id, 'm1_item_id='+nextid, $next.children('td.movelinks').html()));
							}
						}
					}
				}
			}

			/* Event-Handling functions */
			function DeleteItem($dlink) {
				$dlink = jQuery($dlink.currentTarget);
				var href = $dlink.attr('href') + '&m1_ajax=true&suppressoutput=true';

				if(confirm('{/literal}{$areyousure_item}{literal}') == true) {
					jQuery.ajax({
						url: href,
						success: function(response) {
							var $el = $dlink.parent().parent();
							var id = parseInt($dlink.attr('href').match(idRegExp)[1]);
							var level = parseInt($el.attr('class').match(levelRegExp)[1]);

							$el.fadeOut((effects > 1) ? 500 : 0, function(){
								onDelete($el, id, level);

								// calculate children count for select-operations and then delete the nodes
								var $children = getRowChildren($el, level);
								rowcount -= $children.length + 1;

								selcount -= $children.filter('tr.checked').length + (($el.hasClass('checked')) ? 1 : 0);
								$selcountspan.text(selcount);

								$children.remove();
								$el.remove();

								RecolourRows();
								updateListactions();
							});
						}
					});
				}

				return false;
			}

			function ActivateItem(evt) {
				$alink = jQuery(evt.currentTarget);
				var href = $alink.attr('href') + '&m1_ajax=true&suppressoutput=true';
				jQuery.ajax({
					url: href,
					success: function(response) {
						var responseCode = parseInt(response.substr(0,1));

						if(responseCode == 1) {
							$alink.children().eq(0).add(getRowChildren($alink.parent().parent()).find('a.activate img')).attr('src', str_replace('false', 'true', $alink.children().eq(0).attr('src')));
						} else if(responseCode == 0) {
							$alink.children().eq(0).add(getRowChildren($alink.parent().parent()).find('a.activate img')).attr('src', str_replace('true', 'false', $alink.children().eq(0).attr('src')));
						}
					}
				});

				return false;
			}

			function ExpandItem($elink) {
				$elink = jQuery($elink.currentTarget);

				var href = $elink.attr('href') + '&m1_ajax=true';//&suppressoutput=true';
				var expandRegExp = "m1_expand=([0-9]+)";
				var expand = href.match(expandRegExp)[1];

				jQuery.ajax({
					url: href,
					success: function(response) {
						// response code can be 0 or 1 - 1 means success, 0 means error
						// on success, after the response code follows the childcount of the expanded node
						// if the node is contracted, the script returns an empty error-message meaning the node should be contracted

						var responseCode = response.substr(0,1);
						var $el = $elink.parent().parent();
						var level = $el.attr('class').match(levelRegExp)[1];

						if(responseCode == '1') {															// expand
							var id = $elink.attr('href').match(idRegExp)[1];
							var childcount = parseInt(response.substr(2, response.indexOf(';')-2));
							$el.after(response.substr(response.indexOf(';')+1));

							var $children = getRowChildren($el, level);

							setupEvents($children);
							RecolourRows();
							rowcount += childcount;

							if(effects > 1) $children.css('display', 'none').fadeIn(500);
						} else {										// contract
							var $children = getRowChildren($el, level);
							rowcount -= $children.length;

							if($el.hasClass('checked')) {
								selcount -= $children.filter('tr.checked').length;
								$selcountspan.text(selcount);
							}

							$children.fadeOut((effects > 1) ? 500 : 0, function() {$children.remove();RecolourRows();});
						}

						var $expandlink = $el.find('a.expand');
						var $expandicon = $el.find('a.expand img');

						// toggle expand/contract link
						if(expand == '1') {
							$expandlink.attr('href', str_replace('m1_expand=1', 'm1_expand=0', $expandlink.attr('href')));
							$expandicon.attr('src', str_replace('expand.gif', 'contract.gif', $expandicon.attr('src')));

							if($el.hasClass('checked')) {
								checkRow($el.find('input'), true, false);
							}
						} else {
							$expandlink.attr('href', str_replace('m1_expand=0', 'm1_expand=1', $expandlink.attr('href')));
							$expandicon.attr('src', str_replace('contract.gif', 'expand.gif', $expandicon.attr('src')));
						}
					},
				});

				return false;
			}

			function MoveItem($mlink) {
				$mlink = jQuery($mlink.currentTarget);
				var href = $mlink.attr('href') + '&m1_ajax=true&suppressoutput=true';
				var id = href.match(idRegExp)[1];

				jQuery.ajax({
					url: href,
					success: function(response) {
						var responseCode = response.substr(0,1);

						if(responseCode == 1) {
							var $parent = $mlink.parents('tr');
							var level = $parent.attr('class').match(levelRegExp)[1];

							var $movelinks = $parent.children('td.movelinks').clone(true);

							// get "real" previous / next sibling and swap places
							if($mlink.attr('rel') == 'up') {
								var $prev = $parent.prevAll('tr.level'+level).eq(0);
								var prevhref = $prev.find('a.movelink').eq(0).attr('href');
								var previd = parseInt(prevhref.match(idRegExp)[1]);

								$parent.children('td.movelinks').replaceWith($prev.children('td.movelinks').clone(true));
								$parent.children('td.movelinks').html(str_replace('m1_item_id='+previd, 'm1_item_id='+id, $parent.children('td.movelinks').html()));

								$prev.children('td.movelinks').replaceWith($movelinks);
								$prev.children('td.movelinks').html(str_replace('m1_item_id='+id, 'm1_item_id='+previd, $prev.children('td.movelinks').html()));

								var $newparent = $parent.clone(true).insertBefore($prev);
								getRowChildren($parent, level).insertBefore($prev);

								setupEvents($newparent.add($prev));
								$parent.remove();
							} else {
								var $next = $parent.nextAll('tr.level'+level).eq(0);
								var nexthref = $next.find('a.movelink').eq(0).attr('href');
								var nextid = parseInt(nexthref.match(idRegExp)[1]);

								$parent.children('td.movelinks').replaceWith($next.children('td.movelinks').clone(true));
								$parent.children('td.movelinks').html(str_replace('m1_item_id='+nextid, 'm1_item_id='+id, $parent.children('td.movelinks').html()));

								$next.children('td.movelinks').replaceWith($movelinks);
								$next.children('td.movelinks').html(str_replace('m1_item_id='+id, 'm1_item_id='+nextid, $next.children('td.movelinks').html()));

								setupEvents($parent.add($next.clone(true).insertBefore($parent)));
								getRowChildren($next, level).insertBefore($parent);

								var $newparent = $parent;
								$next.remove();
							}

							RecolourRows();

							if(effects > 0) $newparent.css('display', 'none').fadeIn(500);
						} else {
							//alert('FEHLER');
						}
					}
				});
				return false;
			}

			function SubmitList(evt) {
				var $form = jQuery(evt.currentTarget).parents('form');
				var $rows = jQuery('#itemlist tr.checked');
				var action = parseInt(document.getElementById('m1_listaction').value);
				var submit = false;

				if(selcount > 0) {
					switch (action) {
						case 0:
							if((selcount == 1 ? confirm('{/literal}{$areyousure_item}{literal}') : confirm(str_replace('%num', selcount, '{/literal}{$areyousure_items}{literal}'))) == true)
								submit = true;
						break;
						case 1: case 2:
							submit = true;
						break;
					}
				}

				if(submit === true) {
					if(window.location.href.indexOf('edit_category') != -1) {
						var $item_desc = jQuery('textarea[name="m1_item_desc"]');
						var buffer = $item_desc.html();
						$item_desc.html('');
						var data = str_replace('edit_category', 'listactions', $form.serialize());
						$item_desc.html(buffer);
					} else {
						data = $form.serialize();
					}

					jQuery.ajax({
						type: 'POST',
						data: data,
						url: $form.attr('action'),
						success: function(response) {
							var responseCode = response.substr(0,1);

							if(responseCode == 1) {

								switch(action) {
									case 0:
										$rows.each(function() {
											var $row = jQuery(this);
											getRowChildren($row).fadeOut((effects > 1) ? 500 : 0, function() {jQuery(this).remove();});
											onDelete($row, $row.attr('id').substr(3), parseInt($row.attr('class').match(levelRegExp)[1]));
											$row.remove();
										});
										$rows.remove();
										RecolourRows();

										selcount = 0;
										$selcountspan.text(selcount);

										updateListactions();
									break;

									case 1:
										$rows.find('a.activate img').attr('src', str_replace('false', 'true', $rows.find('a.activate img').eq(0).attr('src')));
									break;

									case 2:
										$rows.find('a.activate img').attr('src', str_replace('true', 'false', $rows.find('a.activate img').eq(0).attr('src')));
									break;
								}
							}
						},
					});
				}

				return false;
			}

			function updateListactions() {
				jQuery('#selectall').attr('checked', (selcount == rowcount) ? true : false);
				if(selcount > 0) {
					jQuery('#m1_listaction').removeAttr('disabled');
				} else {
					jQuery('#m1_listaction').attr('disabled', 'disabled');
				}
			}

			// checks / unchecks the specified row and all children (children are locked if parent is checked)
			function checkRow($el, mode, setcount) {
				var $row = $el.parent().parent();
				var level = parseInt($row.attr('class').match(levelRegExp)[1]);
				var $children = getRowChildren($row, level);

				if(!$row.hasClass('checked') || mode === true) {
					$row.addClass('checked');

					$children.find('input').attr('checked', true).attr('disabled', true).parent().parent().addClass('disabled');
					$children = $children.not('tr.checked').addClass('checked');

					selcount += $children.length + ((setcount !== false) ?  1 : 0);
				} else {
					$row.removeClass('checked');

					if(unselectChildren === 1) {
						$children = $children.filter('tr.checked').removeClass('checked');
						$children.find('input').attr('checked', false).attr('disabled', false).parent().parent().removeClass('disabled');
						selcount -= $children.length + ((setcount !== false) ? 1 : 0);
					} else {
						$children.filter('tr.level'+(level+1)).find('input').attr('disabled', false).parent().parent().removeClass('disabled');
						selcount -= 1;
					}
				}

				$selcountspan.text(selcount);
				updateListactions();
			}

			function checkAll($btn) {
				var $rows = jQuery('#itemlist tr');
				var $inputs = $rows.find('input');

				if($btn.attr('checked') != true) {
					$rows.removeClass('checked');
					$inputs.attr('checked', false);
					selcount = 0;
				} else {
					$rows.addClass('checked');
					$inputs.attr('checked', true);
					selcount = $rows.length;
				}

				$inputs.attr('disabled', false).parent().parent().removeClass('disabled');
				$rows.filter('tr.checked').not('tr.level'+rootlevel).find('input').attr('disabled', 'disabled').parent().parent().addClass('disabled');

				$selcountspan.text(selcount);
				updateListactions();
			}

			/*function reverseSelect() {
				var $rows = jQuery('#itemlist tr');
				var $selected = $rows.filter('tr.checked');
				var $unselected = $rows.not('tr.checked');

				$selected.removeClass('checked');
				$selected.find('input').attr('checked', false);

				$unselected.addClass('checked');
				$unselected.find('input').attr('checked', true);

				$rows.find('input').attr('disabled', '');

				selcount = rowcount - selcount;
				$selcountspan.text(selcount);

				updateListactions();
			}*/
		-->
		</script>
		{/literal}
		<div class="itemlist">
			<table cellspacing="0" class="pagetable itemlist" style="max-width: 800px; margin: 10px 20px 10px 15px">
				<thead>
					<tr style="text-align:center">
						<th style="width: 15px"></th>
						<th>{$th_name}</th>
						{* <th>{$th_type}</th> *}
						<th style="width: 10px">{$th_id}</th>
						<th style="width: 10px">{$th_active}</th>
						<th class="pagepos" style="width: 10px">{$th_reorder}</th>
						<th class="pageicon" style="width: 10px">{$th_actions}</th>
						<th style="width: 10px"></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="5" style="text-align: right">{$unselect_children}:&nbsp;&nbsp;<input type="checkbox" onclick="unselectChildren *= -1" style="vertical-align:middle" /></th>
						<th colspan="2" style="text-align: right">{$all}:&nbsp;&nbsp;<input type="checkbox" onclick="checkAll(jQuery(this))" style="vertical-align:middle" id="selectall" /></th>
					</tr>
				</tfoot>
				<tbody id="itemlist">
					{$dlm_rows}
				</tbody>
			</table>

			<div style="max-width: 800px; text-align: right; margin: 0 0 -60px 15px">
				{*<input type="button" value="{$reverse_selection}" onclick="reverseSelect()" style="margin-right: 0px" />*}
				{$selected} (<span id="selcount">0</span>):
				<select name="m1_listaction" id="m1_listaction">
					<option value="0">{$delete}</option>
					<option selected="selected" value="1">{$activate}</option>
					<option value="2">{$deactivate}</option>
					{*<option value="3" disabled="disabled">{$move}</option>
					<option value="4" disabled="disabled">{$suborder}</option>*}
				</select>
				<br />
				<input type="submit" style="margin: 5px 0pt 0pt" value="Absenden" id="m1_listsubmit" name="m1_listsubmit" {*onclick="submitItemlist();return false"*} />
			</div>
		</div><br />
	{else}
		<h4>{$no_children}</h4><br />
	{/if}
{/capture}

{if $showlist === true}
	{$itemlist}
{/if}