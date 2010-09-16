{$ajax_head}
{$startform}
<div id="dlmpage" style="margin-left:2%">
{$hidden}
	{if isset($headline)}<h3>{$headline}</h3>{/if}
	{if strlen($path[0]) > 0}
		<p>
			<strong>&nbsp;&nbsp;&nbsp;&nbsp;{$th_path}</strong>:&nbsp;{$path[0]}
		</p><br />
	{/if}

	<p>
		<label for="m1_item_name">{$th_name}: *</label>
		<input type="text" maxlength="255" size="60" value="{$name_value}" id="m1_item_name" name="m1_item_name" class="req" />
	</p><br />

	<p>
		<label for="m1_item_parent">{$th_parent}:</label>
		{$parent_input}
	</p><br />

	<div style="max-width: 600px; display: table-row">
	{capture assign="extensions"}
		{if isset($th_allowed)}({$th_allowed}: {$allowed_list}){elseif isset($th_forbidden)}({$th_forbidden}: {$forbidden_list}){/if}
	{/capture}
	{if !empty($th_uploads)}
		<div style="display:table-cell">
			<label for="m1_item_upload">{$th_uploads}: {$extensions}</label>
			<input type="file" size="42" name="m1_item_upload" id="m1_item_upload" class="loc loc" />
		</div>
		<div style="display:table-cell; width: 50px; text-align: center; vertical-align: middle">
			<span style="color:#ff0000; font-weight: bold">
				{$or}
			</span>
		</div>
		<div style="display:table-cell">
			<label for="m1_item_location">{$th_location}:</label>
			<input type="text" size="100" value="{$location_value}" id="m1_item_location" name="m1_item_location" class="loc url" />
		</div>
	{else}
		<p>
			<label for="m1_item_location">{$th_location}: {$extensions}*</label>
			<input type="text" size="100" value="{$location_value}" id="m1_item_location" name="m1_item_location" class="req url" />
			{$edit_location}
		</p>
	{/if}
	</div><br /><br />

	<p>
		<label for="m1_item_filesize">{$th_filesize}:</label>
		<input type="text" size="42" value="{$filesize_value}" id="m1_item_filesize" name="m1_item_filesize" class="int" />
	</p><br />

	<div>
		<label for="addmirror">{$th_mirror}: {if is_array($mirrors)}&nbsp; (<a href="" onclick="jQuery('#mirrors').toggle((effects > 0) ? 500 : 0);return false">{$toggle}</a>){/if}</label>
		<ul id="mirrors" style="padding: 0">{if is_array($mirrors)}{$mirrorlist}{/if}</ul><input type="button" id="addmirror" value="{$add_mirror}" style="float: left" />
		<br style="clear:left" />
	</div><br />
	<div id="hideroot" style="display:none"></div>

	<p>
		<label for="m1_item_desc">{$th_desc}:</label>
		<textarea rows="4" cols="50" name="m1_item_desc" id="m1_item_desc">{$desc_value}</textarea>
	</p>

	<div>
		{$item_type}{$ajax}<br />
		<p>{$submit}{$cancel}{$temp}</p>
	</div>

	<div id="writeroot"></div>
</div>
{$endform}