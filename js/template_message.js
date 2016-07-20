jQuery(function($){
	$(document).ready(function() {
		$('input[name = "attach_dir"]').change(function() {
			if(this.value!='None') {
				$('#diag_place').append('<div id="dialog" title="Be weary">You have to choose game template to attach a game</div>');
				$('#dialog').dialog({
  				close: function( event, ui ) {
  					$('#dialog').remove();
  				}
			});
				$('input[name = "wp_upl_dir[]"]').removeAttr('value');
			}
		});
		$('input[name = "wp_upl_dir[]"]').click(function() {
			$('#diag_place').append('<div id="dialog" title="Be weary">You have to choose game template to upload files. <br> Also, You are required to enter a folder name to create it.</div>')
			$('#dialog').dialog({
  				close: function( event, ui ) {
  					$('#dialog').remove();
  				}
			});
			$('input:radio[name = "attach_dir"][value = "None"]').prop("checked", true);
		});
	});
});