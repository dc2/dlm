{$ajax_head}
{$startform}
{if isset($headline)}<h3>{$headline}</h3>{/if}
<div id="dlmpage" style="margin-left:2%">
	<p>
		<label for="m1_tpl_name">{$name_text}: *</label>
		<input type="text" maxlength="255" size="60" value="{$name_value}" id="m1_tpl_name" name="m1_tpl_name" class="req" />
	</p>

	<p class="pageinput">
		<label for="m1_tpl_overview">{$overview_text}:</label>
		<textarea rows="4" cols="50" name="m1_tpl_overview" id="m1_tpl_overview">{$overview_value}</textarea>
	</p><br />

	<p class="pageinput">
		<label for="m1_tpl_detail">{$detail_text}:</label>
		<textarea rows="4" cols="50" name="m1_tpl_detail" id="m1_tpl_detail">{$detail_value}</textarea>
	</p>

	<div>
		{$ajax}{$hidden}<br />
		<p>{$submit}{$cancel}{$temp}</p>
	</div>

	<div id="writeroot">

	</div>
</div>
{$endform}