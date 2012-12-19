lang_set = '{"az":"азербайджанский","sq":"албанский","en":"английский","ar":"арабский","hy":"армянский","af":"африкаанс","eu":"баскский","be":"белорусский","bn":"бенгальский","bg":"болгарский","cy":"валлийский","hu":"венгерский","vi":"вьетнамский","gl":"галисийский","nl":"голландский","el":"греческий","ka":"грузинский","gu":"гуджарати","da":"датский","iw":"иврит","yi":"идиш","id":"индонезийский","ga":"ирландский","is":"исландский","es":"испанский","it":"итальянский","kn":"каннада","ca":"каталанский","zh-TW":"китайский (традиционный)","zh-CN":"китайский (упрощенный)","ko":"корейский","ht":"креольский (Гаити)","lo":"лаосский","la":"латынь","lv":"латышский","lt":"литовский","mk":"македонский","ms":"малайский","mt":"мальтийский","de":"немецкий","no":"норвежский","fa":"персидский","pl":"польский","pt":"португальский","ro":"румынский","ru":"русский","sr":"сербский","sk":"словацкий","sl":"словенский","sw":"суахили","tl":"тагальский","th":"тайский","ta":"тамильский","te":"телугу","tr":"турецкий","uk":"украинский","ur":"урду","fi":"финский","fr":"французский","hi":"хинди","hr":"хорватский","cs":"чешский","sv":"шведский","eo":"эсперанто","et":"эстонский","ja":"японский"}';
lang_set = JSON.parse(lang_set);

count = function(obj){
	var size = 0, key;
	for ( key in obj ) {
		if ( obj.hasOwnProperty(key) )
			size++;
	}
	return size;
}

in_object = function(haystack, needle) {
	for (el in haystack) {
		if ( haystack[el] == needle )
			return true;
	}
}

// SAVE Options
$('#save_options').on('click', function () {

	var output = { 'lang_enabled' : {}, 'domain_lang' : {} }, c=0;

	$('#lanuages_options_data tbody tr').each( function(){

		var domain = $(this).find('input[name=domain]').val(),
			lang_domain = $(this).find('select[name=lang]').val(),
			enabled = $(this).find('input[type=checkbox]')[0].checked==true? 1 : 0;

			if (enabled) {
				if ( !in_object(output['lang_enabled'], lang_domain) )
					output['lang_enabled'][c++] = lang_domain;
			}
			output['domain_lang'][domain] = lang_domain;
	});

	$('#tr_options [name=lang_options]').val(JSON.stringify(output));
	$('#tr_options').submit();

	return false;
});

// ADD Language
$('.add_language').on('click', function(){
	
	var line = $('<tr><td><input type="text" name="domain"></td></tr>'),
		td2 = $('<td></td>'), 
		td3 = $('<td style="text-align:center;"><input type="checkbox" name="enabled"></td><td><div class="remove_language"></div></td>'),
		select = $('<select name="lang"></select>');

	for ( lang_code in lang_set ) {
		var option = $('<option></option>');
		option.val(lang_code);
		option.html(lang_set[lang_code]);
		select.append(option);
	}

	line.append(td2);
	td2.append(select);
	line.append(td3);

	$('#lanuages_options_data table').append(line);
});

// REMOVE Language
$('.remove_language').live('click', function(){	$(this).closest('tr').remove(); });