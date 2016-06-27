jQuery(function($){
	$(document).ready(function() {
		$('input[name = "attach_dir"]').change(function() {
			if(this.value!='None') {
				$('#dialog').dialog();
				$('input[name = "wp_upl_dir[]"]').removeAttr('value');
			}
		});
		$('input[name = "wp_upl_dir[]"]').click(function() {
			$('#dialog').dialog();
			$('input:radio[name = "attach_dir"][value = "None"]').prop("checked", true);
		});
	});
});