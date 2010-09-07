{$ajax_head}
{$startform}
<div id="dlmpage" style="margin-left:2%">
{$hidden}
	{if isset($headline)}
		<h3>{$headline}</h3>
	{/if}
	{if strlen($path[0]) > 0}
		<p>
			<strong>&nbsp;&nbsp;&nbsp;&nbsp;{$path_text}</strong>:&nbsp;{$path[0]}
		</p>
		<br />
	{/if}

	<p>
		<label for="m1_item_name">{$name_text}: *</label>
		<input type="text" maxlength="255" size="60" value="{$name_value}" id="m1_item_name" name="m1_item_name" class="req" />
	</p><br />

	<p>
		<label for="m1_item_parent">{$parent_text}:</label>
		{$parent_input}
	</p><br />

	{if $itemcount > 0}
		<div style="margin-top: 20px">
			<h4>{$node_children}: <a href="" onclick="jQuery('div.itemlist').toggle((effects > 0) ? 500 : 0);return false">{$toggle}</a></h4>
			{$itemlist}
		</div>
	{/if}
	<div style="margin-left: 15px">
		{$add_category}&nbsp;{$add_download}
	</div><br /><br />

	<p>
		<label for="m1_item_desc">{$desc_text}:</label>
		<textarea rows="4" cols="50" name="m1_item_desc" id="m1_item_desc">{$desc_value}</textarea>
	</p>

	<div>
		{$item_type}{$ajax}<br />
		<p>{$submit}{$cancel}{$temp}{$view}</p>
	</div>
	<div id="writeroot"></div>
</div>
{$endform}