lang_set = '{"ab":"абхазский","av":"аварский","ae":"авестийский","az":"азербайджанский","ay":"аймара","ak":"акан","sq":"албанский","am":"амхарский","en":"английский","ar":"арабский","hy/ am":"армянский","as":"ассамский","aa":"афарский","af":"африкаанс","bm":"бамбара","eu":"баскский","ba":"башкирский","be":"белорусский","bn":"бенгальский","my":"бирманский","bi":"бислама","bg":"болгарский","bs":"боснийский","br":"бретонский","cy":"валлийский","hu":"венгерский","ve":"венда","vo":"волапюк","wo":"волоф","vi":"вьетнамский","gl":"галисийский","lg":"ганда","hz":"гереро","kl":"гренландский","el":"греческий (новогреческий)","ka":"грузинский","gn":"гуарани","gu":"гуджарати","gd":"гэльский","da":"датский","dz":"дзонг-кэ","dv":"дивехи (мальдивский)","zu":"зулу","he":"иврит","ig":"игбо","yi":"идиш","id":"индонезийский","ia":"интерлингва","ie":"интерлингве","iu":"инуктитут","ik":"инупиак","ga":"ирландский","is":"исландский","es":"испанский","it":"итальянский","yo":"йоруба","kk":"казахский","kn":"каннада","kr":"канури","ca":"каталанский","ks":"кашмири","qu":"кечуа","ki":"кикуйю","kj":"киньяма","ky":"киргизский","zh":"китайский","kv":"коми","kg":"конго","ko":"корейский","kw":"корнский","co":"корсиканский","xh":"коса","ku":"курдский","km":"кхмерский","lo":"лаосский","la":"латинский","lv":"латышский","ln":"лингала","lt":"литовский","lu":"луба-катанга","lb":"люксембургский","mk":"македонский","mg":"малагасийский","ms":"малайский","ml":"малаялам","mt":"мальтийский","mi":"маори","mr":"маратхи","mh":"маршалльский","mo":"молдавский","mn":"монгольский","gv":"мэнский (мэнкский)","nv":"навахо","na":"науру","nd":"ндебеле северный","nr":"ндебеле южный","ng":"ндунга","de":"немецкий","ne":"непальский","nl":"нидерландский (голландский)","no":"норвежский","ny":"ньянджа","nn":"нюнорск (новонорвежский)","oj":"оджибве","oc":"окситанский","or":"ория","om":"оромо","os":"осетинский","pi":"пали","pa":"пенджабский","fa":"персидский","pl":"польский","pt":"португальский","ps":"пушту","rm":"ретороманский","rw":"руанда","ro":"румынский","rn":"рунди","ru":"русский","sm":"самоанский","sg":"санго","sa":"санскрит","sc":"сардинский","ss":"свази","sr":"сербский","si":"сингальский","sd":"синдхи","sk":"словацкий","sl":"словенский","so":"сомали","st":"сото южный","sw":"суахили","su":"сунданский","tl":"тагальский","tg":"таджикский","th":"тайский","ty":"таитянский","ta":"тамильский","tt":"татарский","tw":"тви","te":"телугу","bo":"тибетский","ti":"тигринья","to":"тонганский","tn":"тсвана","ts":"тсонга","tr":"турецкий","tk/ tu":"туркменский","uz":"узбекский","ug":"уйгурский","uk":"украинский","ur":"урду","fo":"фарерский","fj":"фиджи","fi":"финский","fr":"французский","fy":"фризский","ff":"фулах","ha":"хауса","hi":"хинди","ho":"хиримоту","cu":"церковнославянский","ch":"чаморро","ce":"чеченский","cs":"чешский","za":"чжуанский","cv":"чувашский","sv":"шведский","sn":"шона","ee":"эве","eo":"эсперанто","et":"эстонский","jv":"яванский","ja":"японский"}';
lang_set = JSON.parse(lang_set);

size = function(obj){
	var size = 0, key;
	for ( key in obj ) {
		if ( obj.hasOwnProperty(key) )
			size++;
	}
	return size;
}

search = function(obj, element) {
	for (el in obj) {
		if ( obj[el] == element )
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
				if ( !search(output['lang_enabled'], lang_domain) )
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