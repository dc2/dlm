{*
{capture assign="mirrorlist"}
	<table cellspacing="0" class="pagetable" style="max-width: 700px; margin: 0 0 0 15px">
	<thead>
		<tr>
			<th>Mirror</th>
			<th>Link</th>
			<th>Downloads</th>
			<th>Aktionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$mirrors item=mir}
		{cycle values="row1,row2" assign=rowclass}
		<tr class="{$rowclass}">
			<td>
				{$mir.name|escape}
			</td>
			<td>
				<a href="{$mir.location|htmlentities}" title="{$mir.name} ({$mir.location|htmlentities})">{$mir.location|htmlentities|truncate:80}</a>
			</td>
			<td style="width: 10px; text-align: center">
				{$mir.downloads}
			</td>
			<td style="width: 10px; text-align: center">
				{$mir.editlink}{$mir.deletelink}
			</td>
		</tr>
		{/foreach}
	</tbody>
	</table>
{/capture}
*}

{capture assign="mirrorlist"}
	{foreach from=$mirrors item=mir}
	{cycle values="row1,row2" assign=rowclass}
	<li class="{$rowclass}"><span class="right">{*<a class="lft">&lt;</a>&nbsp;<a class="rgt">&gt;</a>&nbsp;&nbsp;*}<a class="del">x</a></span>{$mirror_name}:<br />
		<input type="text" class="req" name="m1_mirror_names[]" size="60" value="{$mir.name|escape}" /><br />
		{$mirror_url}:<br />
		<input type="text" class="req url" name="m1_mirror_urls[]" size="60" value="{$mir.location|htmlentities}" />
		<input type="hidden" name="m1_mirror_ids[]" value="{$mir.ID}" />
	</li>
	{/foreach}
{/capture}