<script type="text/javascript">
<!--{literal}
	jQuery(function() {
		jQuery('a.deletetpl').unbind().click(DeleteTemplate);
		jQuery('a.defaulttpl').unbind().click(SetDefaultTemplate);
	});

	function DeleteTemplate($dlink) {
		$dlink = jQuery($dlink.currentTarget);
		var href = $dlink.attr('href') + '&m1_ajax=true&suppressoutput=true';

		if(confirm('{/literal}{$areyousure_tpl}{literal}') == true) {
			jQuery.ajax({
				url: href,
				success: function(response) {
					var $el = $dlink.parent().parent();
					$el.fadeOut((effects > 1) ? 500 : 0);
				}
			});
		}

		return false;
	}

	function SetDefaultTemplate($dlink) {
		$dlink = jQuery($dlink.currentTarget);
		var href = $dlink.attr('href') + '&m1_ajax=true&suppressoutput=true';

		jQuery.ajax({
			url: href,
			success: function(response) {
				var $imgs = $dlink.parent().parent().parent().find('a.defaulttpl img').not($dlink.children('img'));
				$imgs.attr('src', str_replace('true.gif', 'false.gif', $dlink.children('img').attr('src')));
				$dlink.children('img').attr('src', str_replace('false.gif', 'true.gif', $dlink.children('img').attr('src')));
				RecolourRows('.pagetable');
			}
		});

		return false;
	}
{/literal}-->
</script>

<table class="pagetable" style="max-width: 800px; margin: 10px 20px 10px 15px">
	<thead>
		<tr>
			<th>{$th_templates}</th>
			<th style="width:10px">{$th_default}</th>
			<th style="width:10px">{$th_actions}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$templates item=tpl}
	{cycle values="row1,row2" assign=rowclass}
		<tr class="{$rowclass}">
		<td>{$tpl.name}</td>
		<td style="text-align:center">{$tpl.default}</td>
		<td style="text-align:center">{if trim($tpl.import) != ''}{$tpl.import}{else}{$tpl.edit}&nbsp;&nbsp;{$tpl.delete}{/if}</td></tr>
	{/foreach}
	</tbody>
</table>
{$add_template}