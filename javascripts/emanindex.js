window.jQuery = window.$ = jQuery;

jQuery(document).ready(function() {
	$('#checkall').change(function() {
		var state = $(this).prop('checked');
		$('.check-available').each(function (i, el) {
			$(el).prop('checked', state);
		});
	});
  $('.montrer').click(function() {
    $(this).next('ol.records').toggle();
    $(this).next().next('ol.records').toggle();
    if ($(this).text() == ' + ') {
      $(this).text(' - ');
    } else {
      $(this).text(' + ');
    }
  });
  $('.tout').click(function() {
    $('ol.records').toggle();
    if ($(this).html() == 'Tout replier') {
      $(this).css('background', '#A4C637');
      $(this).css('border-color', '#749308');
      $(this).html('Tout d&eacute;plier');
      $('.montrer').html(' + ');
    } else {
      $(this).css('background', '#ad6345');
      $(this).css('border-color', '#7E432C');
      $(this).html('Tout replier');
      $('.montrer').html(' - ');
    }
  });
  $('.edit-value').click(function(event) {
    event.stopPropagation();
    $("textarea.value").remove();
    $("input.enregistrer").remove();
    if($(this).parent().find('input.value').length == 0) {
      $(this).parent().append("<textarea class='value' name='value'></textarea><input type='button' class='enregistrer' value='Enregistrer'>");
      text = exactHTML($(this).parent().parent().parent().find('span.html-value'));
      $(this).parent().find('textarea.value').val(text);
    }
  });
  $('#wrap').on('click', '.enregistrer', function(e) {
    recordType = $(this).parent().parent().parent().attr('type');
    console.log('type : ' + recordType);
    anchorId = $(this).parent().parent().parent().attr('id');
    recordId = $(this).parent().attr('id');
    elementId = $( "#fieldName" ).val();
    valeur = $(this).parent().find('.value').val();
    orig = exactHTML($(this).parent().parent().parent().find('span.html-value'));
    if (orig == 'Non renseigné') {
      orig = '';
    }
    path = window.location.pathname;
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: path.substr(0, path.lastIndexOf("/")) + '/emanindexupdate',
      data: { recordId: recordId, elementId: elementId, valeur: valeur, orig: orig, recordType: recordType },
      success: function(json) {
        document.location = window.location.origin + window.location.pathname  + window.location.search + '#' + recordType + '-' + anchorId;
        document.location.reload(true);
      }
    });
  });
});

// Needed to get innerHTML with correct self-closing tags
function exactHTML(element) {
  var serialize = new XMLSerializer();
  text = serialize.serializeToString(element.get(0));
  text = text.substring(text.indexOf(">") + 1, text.lastIndexOf("</span>"));
  return text;
}