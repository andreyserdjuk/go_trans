$('#save_options').on('click', function () {
	
	var container = new Array();

	container['slug'] = $('input[name=slug]');
	container['id'] = $('input[name=id]');
	languages = $('[name=language]');

	var output = {};

	for ( el in container ) {

		for ( var j=0; j<container[el].length; j++ ) {

			if (typeof output[languages[j].value] != 'object')
				output[languages[j].value] = {};
			
			output[languages[j].value][el] = container[el][j].value;
		}
	}

	var checkboxes = $('input[name=enabled]');

	for ( var j=0; j<checkboxes.length; j++ ) {

		output[languages[j].value]['enabled'] = checkboxes[j].checked==true? 1 : 0;
	}
	output = JSON.stringify(output)
	$('input[name=lanuages_options]').val(output);
	$('input[name=translate]').val("no");
	$('#options_serialized').submit();
});