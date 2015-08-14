$( document ).ready(function() {
    $( "a.delete" ).on( "click", function( event) {
  		event.preventDefault(); 
  		var tr = $(this).closest('tr');
  		var id = tr.children('td:first').html();

  		$('#info').load('ajax-request.php?del='+id, 
  										tr.fadeOut('slow', function() {
  											$(this).remove();
  										}));
  		// tr.hide('slow');
		});
});