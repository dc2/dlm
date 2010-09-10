{capture assign="ajax_head"}
{literal}
<script src="../modules/DLM/libs/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../modules/DLM/libs/js/jquery-ui-1.8.2.custom.min.js" type="text/javascript"></script>
<script src="../modules/DLM/libs/js/jquery.form.min.js" type="text/javascript"></script>
<script src="../modules/DLM/libs/js/shortcut.min.js" type="text/javascript"></script>

<script type="text/javascript"> 
<!--	
	
	function validateForm($form) {
		var $inputs = $form.find('input:text').not('input:hidden');
		var error = false;
		
		$inputs.removeClass('error');
		
		var $loc = $inputs.filter('input.loc');
		if($loc.val() == '' && $loc.eq(1).val() == '') {
			error = true;
			$loc.addClass('error');
		} else if($loc.val() == '' && !validateUrl($loc.eq(1).val())) {
			error = true;	
			$loc.eq(1).addClass('error');
		}
		
		$inputs.not('input.loc').each(function(index, $el){
			$el = jQuery($el);

			if($el.hasClass('req')) {
				if(jQuery.trim($el.val()) == '') {
					$el.addClass('error');
					error = true;
				}
			}
			
			if($el.hasClass('url') && !validateUrl($el.val())) {
				$el.addClass('error');
				error = true;
			}
			
			if(jQuery.trim($el.val()) != '' && $el.hasClass('int')) {
				$el.val(str_replace('.', '', $el.val()));
				if(parseInt($el.val()) != $el.val()) {	
					$el.addClass('error');
					error = true;
				}	
			}
			
		});
		
		if(error != false) {
			jQuery('#mirrors').show(effects>0?500:0);
			window.scrollTo(0, $form.find('input.error').not('input:hidden').offset().top - 30);
		}
		
		return !error;
	}
	
	jQuery(function() {
		setupMirrorEvents();
		
		jQuery("#mirrors").sortable({placeholder: 'placeholder', tolerance: 'pointer', forcePlaceholderSize: true, opacity: 0.75, revert: effects>0?200:0, distance: 25});
		
		jQuery.ajaxSetup({
			method: 'POST',
			beforeSend: function() {
				jQuery(document.body).addClass('wait');
			},
			complete: function () {
				jQuery(document.body).removeClass('wait');	
			},
		});
		
		shortcut.add("Ctrl+S",function() {
			jQuery('#m1_temp').trigger('click');
			return false;
		}, {'type':'keypress','propagate':false,'target':document});

		jQuery('div.pagecontainer p.pagemessage').css('margin-bottom', '0');
		jQuery('#m1_temp').click(function () {
			var $form = jQuery('#m1_moduleform_1');
			jQuery('#writeroot').css('visibility', 'hidden');
			jQuery('#writeroot').empty().css('display', 'block').css('min-height', '69px').css('visibility', 'visible');
			
			if(validateForm($form) === true) {				
				jQuery('input[name$="ajax"]').val('true').before('<input name="m1_temp_2" id="m1_temp_2" value="true" type="hidden" />').before('<input name="suppressoutput" id="suppressoutput" value="true" type="hidden" />');      
				//jQuery('#writeroot').empty();//fadeOut(100); 
				
				$form.ajaxSubmit({
					forceSync: true,
					beforeSubmit: function(arr) {
						arr.push({'name': 'm1_temp', 'value': 'true'});
					},
					success: function(response) {
						var responseCode = response.substr(0,1);
						
						jQuery('input[name$="ajax"]').val('false');  
						jQuery('#suppressoutput').add('#m1_temp_2').remove();
						
						if(responseCode == '1') {
							jQuery('#readroot .pagemcontainer').clone().appendTo('#writeroot');
							jQuery('#writeroot .pagemessage').html(jQuery('#writeroot .pagemessage').html().replace('%message', response.substr(2)));
							jQuery('#writeroot .pagemessage').css('display', 'none').fadeIn(500); 
						} else {
							jQuery('#readroot .pageerrorcontainer').clone().appendTo('#writeroot');
							jQuery('#writeroot .pageerrorcontainer').html(jQuery('#writeroot .pageerrorcontainer').html().replace('%error', response.substr(2)));
							jQuery('#writeroot .pageerror').css('display', 'none').fadeIn(500); 
						}
						
					}
				});
			}
			
			return false;
		});
		
		jQuery('#m1_submit').click(function(){
			return validateForm(jQuery('#m1_moduleform_1'));
		});
		
		jQuery('#addmirror').click(addMirror);
	}); 
	
	function setupMirrorEvents() {
		jQuery('#mirrors a.del').unbind().click(deleteMirror).attr('title', 'delete');
		//jQuery('#mirrors a.lft').add(jQuery('#mirrors a.rgt')).unbind().click(moveMirror).attr('title', 'move');
	}
	
	function deleteMirror(e) {
		var $mirror = jQuery(e.currentTarget).parent().parent();
		var $inputs = $mirror.children('input');
		var $hidden = $inputs.filter('input:hidden');
		
		if((jQuery.trim($inputs.val()) == '' && jQuery.trim($inputs.eq(1).val()) == '') || confirm('{/literal}{$areyousure_mirror}{literal}') == true) {
			
			
			if($hidden.val() != 'false') {
				$mirror.hide(effects>0?200:0, function(){$hidden.val('delete'+$hidden.val());jQuery('#hideroot').append($mirror)});		
			} else {
				$mirror.hide(effects>0?200:0, function(){jQuery(this).remove()});
			}
		}
	}
	
	function moveMirror(e) {
		$lnk = jQuery(e.currentTarget);
		
		if($lnk.hasClass('lft')) {
			$lnk.parent().parent().insertBefore($lnk.parent().parent().prev());
		} else {
			$lnk.parent().parent().insertAfter($lnk.parent().parent().next());
		}
	}
	
	function addMirror() {
		var $last = jQuery('#mirrors').children(':last');
		var $inputs = $last.find('input');
		
		$last.find('input').removeClass('error');
		
		if($last.length > 0) {
			row = Math.abs(parseInt($last.attr('class').substr(3, 1)-1)-1)+1;
		} else {
			row = 2;
		}
		
		if($last.length == 0 || ($inputs.val().length > 0 && $inputs.eq(1).val().length > 0 && validateUrl($inputs.eq(1).val()))) {
			//jQuery('#mirrors').append(str_replace('%row', 'row'+row, jQuery('#readroot > .mirror').html()));
			var $new = jQuery('#readroot .row').clone().attr('class', 'row'+row);
			jQuery('#mirrors').append($new);
			$new.show(effects>0?200:0);
			
			setupMirrorEvents();
		} else {						
			if($inputs.eq(1).val().length == 0 || !validateUrl($inputs.eq(1).val())) {
				$inputs.eq(1).addClass('error');
			}
			
			if($inputs.val().length == 0) {
				$inputs.eq(0).addClass('error');
			}
		}
		
		return false;
	}
-->	
</script>
{/literal}

<div id="readroot" style="display: none">
	<div class="pagemcontainer">
		<p class="pagemessage">
			<img src="themes/NCleanGrey/images/icons/system/accept.gif" class="systemicon" alt="Erfolgreich abgeschlossen" title="Erfolgreich abgeschlossen" />%message
		</p>
	</div>
	
	<div class="pageerrorcontainer">
		<div class="pageoverflow">
			%error
		</div>
	</div>
	
	<ul class="mirror">
		<li class="row" style="display: none"><span class="right">{*<a class="lft">&lt;</a>&nbsp;<a class="rgt">&gt;</a>&nbsp;&nbsp;*}<a class="del">x</a></span>{$mirror_name}:<br />
			<input type="text" class="req" name="m1_mirror_names[]" size="60" /><br />
			{$mirror_url}:<br />
			<input type="text" class="req url" name="m1_mirror_urls[]" size="60" />
			<input type="hidden" name="m1_mirror_ids[]" value="false" />
		</li>
	</ul>
</div>
{/capture}