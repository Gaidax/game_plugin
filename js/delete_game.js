jQuery(function($){
    $(document).ready(function() {
    	 var to_delete;
    	$('.selector').click(function() {
           to_delete = $(this).attr("id");
           var url = window.location.href;
           window.location.href=url+"&deletion="+to_delete;
        }); 
        var success = message.success_state;
        if(success !== undefined && success !== null) {

    	if(success)  {
    		to_delete = getParameterByName('deletion');
    		$('#diag_place').append('<div id="dialog" title="Deletion">Deleted '+to_delete+'</div>')
			$('#dialog').dialog({
  				close: function( event, ui ) {
  					$('#dialog').remove();
  				}
			});
    	} else if(!success) {
    		to_delete = getParameterByName('deletion');
    		 $('#diag_place').append('<div id="dialog" title="Deletion">Was unable to delete '+to_delete+'. May be the problem with permissions</div>')
			$('#dialog').dialog({
  				close: function( event, ui ) {
  					$('#dialog').remove();
  				}
			});
    	} 
    	        } 
    });
});

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}