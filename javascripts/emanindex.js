window.jQuery = window.$ = jQuery;

jQuery(document).ready(function() {
	$('#checkall').change(function() {
		var state = $(this).prop('checked');
		$('.check-available').each(function (i, el) {
			$(el).prop('checked', state);
		});
	});
	
  $('.montrer').click(function() {
    $(this).next('ol.notices').toggle();
    $(this).next().next('ol.notices').toggle();
    if ($(this).text() == ' + ') {
      $(this).text(' - ');
    } else {
      $(this).text(' + ');      
    }
  });
  $('.tout').click(function() {
    $('ol.notices').toggle();
    if ($(this).html() == 'Tout replier') {
      $(this).html('Tout d&eacute;plier');
      $('.montrer').html(' + ');                
    } else {
      $(this).html('Tout replier');        
      $('.montrer').html(' - ');                
    }
  });    	
});
