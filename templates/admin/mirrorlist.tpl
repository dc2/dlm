{capture assign="mirrorlist"}
	{foreach from=$mirrors item=mir}
	{cycle values="row1,row2" assign=rowclass}
	<li class="{$rowclass}"><span class="right"><a class="del">x</a></span>{$mirror_name}:<br />
		<input type="text" class="req" name="m1_mirror_names[]" size="60" value="{$mir.name|escape}" /><br />
		{$mirror_url}:<br />
		<input type="text" class="req url" name="m1_mirror_urls[]" size="60" value="{$mir.location|htmlspecialchars}" />
		<input type="hidden" name="m1_mirror_ids[]" value="{$mir.ID}" />
	</li>
	{/foreach}
{/capture}

{if $showmirrors == true}
	{$mirrorlist}
{/if}