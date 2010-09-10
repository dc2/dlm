{if !isset($error_none)}
	<div class="dlm">
		{capture assign="pagetitle"}{$pagetitle}{$path[1]}{$headline}{/capture}
		{if strlen($path[0]) > 0}
			<div style="margin-bottom: 15px; font-size: 0.75em"><strong>{$path_text}</strong>: {$path[0]}</div>
		{/if}
		<h3 style="margin-bottom: 5px">{$headline}</h3>
		{if $itemcount > 0}
			{if trim($description) != ''}<p class="description">{$description}</p>{/if}
			<table cellspacing="1" class="default" style="margin: auto; width: 95%">
				<thead>
					<tr style="text-align:center">
						<th>{$th_name}</th>
						{*<th>{$th_type}</th>*}
						{*<th>{$th_id}</th>*}
						<th style="width:20px"><a title="{$th_downloads}">DL</a></th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$items item=entry}
				{cycle values="row1,row2" assign=rowclass}
					<tr class="{$rowclass}">
						<td>
							{$entry->name}
							{if $entry->downloadurl !== false}
								<a href="{$entry->downloadurl}" class="dlbutton_small" style="float:right" rel="nofollow">
									<span>Download</span>
								</a>
							{/if}
						</td>
						<td style="text-align:center">{$entry->downloads}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		{else}
			<strong>{$no_children}</strong><br />
		{/if}
	</div>
{else}
	<strong>{$error_none}</strong>
{/if}<!-- // :::TPL-SEPARATOR::: // -->{if !isset($error_none)}
	<div class="dlm detail">{capture assign="pagetitle"}{$pagetitle}{$path[1]}{$dl_name}{/capture}
		{if strlen($path[0]) > 0}
			<div style="margin-bottom: 15px; font-size: 0.75em">
				<strong>{$path_text}</strong>: {$path[0]}
			</div>
		{/if}

		<h3>{$dl_name}</h3>
		<a href="{$dl_link}" class="dlbutton" rel="nofollow"> <span>Download</span> </a>
		<div style="margin-left: 10px; max-width: 400px">
			<span style="float: right">
				<strong>Datum</strong>: {$dl_date}<br />
				<strong>Traffic</strong>: {$dl_traffic}
			</span>
			<span style="float: left">
				<strong>Dateiname</strong>: {$dl_filename|truncate:25}{$dl_fileext}<br />
				<strong>Dateigröße</strong>: {$dl_size}<br />
				<strong>Downloads</strong>: {$dl_downloads}<br />
			</span>
			<br style="clear: both" />
		</div>

		{if $dl_mirrors !== false}
			<div style="margin-top: 15px">
				<h4>Verfügbare Downloadquellen</h4>
				<table cellspacing="1" class="default" style="margin: auto; width: 95%">
					<thead>
						<tr>
							<th>Mirror</th>
							<th style="width: 1px"><a title="Downloads">DL</a></th>
							<th style="width: 1px">Traffic</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$dl_mirrors item=item key=key}
					{cycle values="row1,row2" assign=rowclass}
						<tr class="{$rowclass}">
							<td><a href="{$dl_mirrorurl|urldecode|replace:'[%mirrorid%]':$key}" rel="nofollow">{$item.name}</a></td>
							<td style="text-align: center">{$item.downloads}</td>
							<td style="text-align: center">{$item.traffic}</td>
						</tr>
					{/foreach}
					</tbody>

				</table>
			</div>
		{/if}


		{if strlen($dl_description) > 0}
			<br />
			<h4 id="Beschreibung">{$dl_description_text}</h4>
			<div style="margin-left: 10px; overflow: hidden" id="dldesc_{$dl_id}">
				{eval var=$dl_description}
			</div>
		{/if}
	</div>
{else}
	<strong>{$error_none}</strong>
{/if}