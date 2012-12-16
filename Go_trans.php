<?php 
/*
Plugin Name: Go_Trans
Plugin URI: none
Description: обходимся без переводов WPML
Version: 0.1
Author: Andrej Serdjuk
Author URI: http://vk.com/lincoln6eco
*/

require_once("go_form.php");
require_once("lib_go_trans.php");

add_action( 'admin_menu', 'register_go_trans_menu_page' );

function register_go_trans_menu_page () {

    add_menu_page( 'Заполнить пустоту переводами', 'Go_Trans', 'add_users', 'Go_Trans/Go_Trans.php', 'go_trans_admin_menu', plugins_url('Go_Trans/images/icon.png'), 3 );
}

function go_trans_admin_menu() {

    add_options_page( 'Заполнить пустоту переводами', 'Go_Trans', 8, 'go_trans_option_page', 'go_trans_option_page' );
    wp_register_script( 'jquery-1.8.3.min', plugins_url('Go_Trans/jquery-1.8.3.min.js') );
    wp_enqueue_script( 'jquery-1.8.3.min' );
    wp_register_script( 'Go_Trans', plugins_url('Go_Trans/Go_Trans.js') );
    wp_enqueue_script( 'Go_Trans' );
    wp_register_style('go_trans_styles', plugins_url('Go_Trans/go_trans_styles.css'));
    wp_enqueue_style('go_trans_styles');

    $go_trans_translate_options = array( 'options' => array( 'post_status' => 'draft' ) );
    $lang_set = '{"ab":"абхазский","av":"аварский","ae":"авестийский","az":"азербайджанский","ay":"аймара","ak":"акан","sq":"албанский","am":"амхарский","en":"английский","ar":"арабский","hy/ am":"армянский","as":"ассамский","aa":"афарский","af":"африкаанс","bm":"бамбара","eu":"баскский","ba":"башкирский","be":"белорусский","bn":"бенгальский","my":"бирманский","bi":"бислама","bg":"болгарский","bs":"боснийский","br":"бретонский","cy":"валлийский","hu":"венгерский","ve":"венда","vo":"волапюк","wo":"волоф","vi":"вьетнамский","gl":"галисийский","lg":"ганда","hz":"гереро","kl":"гренландский","el":"греческий (новогреческий)","ka":"грузинский","gn":"гуарани","gu":"гуджарати","gd":"гэльский","da":"датский","dz":"дзонг-кэ","dv":"дивехи (мальдивский)","zu":"зулу","he":"иврит","ig":"игбо","yi":"идиш","id":"индонезийский","ia":"интерлингва","ie":"интерлингве","iu":"инуктитут","ik":"инупиак","ga":"ирландский","is":"исландский","es":"испанский","it":"итальянский","yo":"йоруба","kk":"казахский","kn":"каннада","kr":"канури","ca":"каталанский","ks":"кашмири","qu":"кечуа","ki":"кикуйю","kj":"киньяма","ky":"киргизский","zh":"китайский","kv":"коми","kg":"конго","ko":"корейский","kw":"корнский","co":"корсиканский","xh":"коса","ku":"курдский","km":"кхмерский","lo":"лаосский","la":"латинский","lv":"латышский","ln":"лингала","lt":"литовский","lu":"луба-катанга","lb":"люксембургский","mk":"македонский","mg":"малагасийский","ms":"малайский","ml":"малаялам","mt":"мальтийский","mi":"маори","mr":"маратхи","mh":"маршалльский","mo":"молдавский","mn":"монгольский","gv":"мэнский (мэнкский)","nv":"навахо","na":"науру","nd":"ндебеле северный","nr":"ндебеле южный","ng":"ндунга","de":"немецкий","ne":"непальский","nl":"нидерландский (голландский)","no":"норвежский","ny":"ньянджа","nn":"нюнорск (новонорвежский)","oj":"оджибве","oc":"окситанский","or":"ория","om":"оромо","os":"осетинский","pi":"пали","pa":"пенджабский","fa":"персидский","pl":"польский","pt":"португальский","ps":"пушту","rm":"ретороманский","rw":"руанда","ro":"румынский","rn":"рунди","ru":"русский","sm":"самоанский","sg":"санго","sa":"санскрит","sc":"сардинский","ss":"свази","sr":"сербский","si":"сингальский","sd":"синдхи","sk":"словацкий","sl":"словенский","so":"сомали","st":"сото южный","sw":"суахили","su":"сунданский","tl":"тагальский","tg":"таджикский","th":"тайский","ty":"таитянский","ta":"тамильский","tt":"татарский","tw":"тви","te":"телугу","bo":"тибетский","ti":"тигринья","to":"тонганский","tn":"тсвана","ts":"тсонга","tr":"турецкий","tk/ tu":"туркменский","uz":"узбекский","ug":"уйгурский","uk":"украинский","ur":"урду","fo":"фарерский","fj":"фиджи","fi":"финский","fr":"французский","fy":"фризский","ff":"фулах","ha":"хауса","hi":"хинди","ho":"хиримоту","cu":"церковнославянский","ch":"чаморро","ce":"чеченский","cs":"чешский","za":"чжуанский","cv":"чувашский","sv":"шведский","sn":"шона","ee":"эве","eo":"эсперанто","et":"эстонский","jv":"яванский","ja":"японский"}';
    $lang_set = json_decode($lang_set, true);

    add_option( 'domain_lang', array('rus'=>'ru'));
    add_option( 'lang_enabled', array('ru'));

    $msg = dispatchPost( $lang_set );
    $domain_lang = get_option('domain_lang');
    if ( gettype($domain_lang)!='array' ) $domain_lang = array('rus'=>'ru');
    $lang_enabled = get_option('lang_enabled');
    if ( gettype($lang_enabled)!='array' ) $lang_enabled = array('ru');

    get_main_form( $domain_lang, $lang_enabled, $lang_set, $msg );
}

function dispatchPost ( $lang_set ) {

	if ( !empty($_POST['lang_options']) ) {

		$options = json_decode(stripcslashes($_POST['lang_options']), true);
		update_option( 'lang_enabled', $options['lang_enabled'] );
		update_option( 'domain_lang', $options['domain_lang'] );
		updateCategoriesList( $options['domain_lang'], $lang_set );
	}

	if ( !empty($_POST['post_status']) ) {
		translate_posts( $_POST['post_status'] );
	}

	if ( !empty($_POST['delete_translated_posts']) ) {
		delete_translated_posts( $_POST['delete_translated_posts'] );
	}
}

function updateCategoriesList ( $lang_list, $lang_set ) {

	// gather array with absent categories
	$cats_to_create = array();
	$all_cats = get_all_category_ids(); // get all id's of categories

	if (!in_array('ru', $lang_list)) // we need russian as source, later source language will be optional
		$lang_list[] = 'ru';

	foreach ( $lang_list as $lang ) {
		$cat = get_term_by( 'name', $lang, 'category', ARRAY_A );
        if ( !$cat ) {
            $cats_to_create[] = $lang;
        }
	}

	foreach ( $cats_to_create as $lang ) {
		wp_insert_category(array( 'cat_name' => $lang, 'category_description' => $lang_set[$lang], 'category_nicename' => $lang, 'taxonomy' => 'category' ) );
	}

	foreach ( $all_cats as $cat_id ) {
		$cat = get_term_by( 'id', $cat_id, 'category', ARRAY_A );
		if ( !in_array($cat['name'], $lang_list) && $cat['name'] != 'ru' )
        	wp_delete_category($cat_id);
	}
}

function translate_posts ( $post_status=false, $post_id=false ) {

	if ( !$post_id )
	    $russian_posts = get_posts( array(
	    'offset'            =>    0,
	    'category'          =>    'ru',
	    'post_type'         =>    'post') );
	else
		$russian_posts[] = get_post( $post_id );

	$lang_enabled = get_option('lang_enabled');

    foreach ( $russian_posts as $post ) { setup_postdata($post);
    	
    	$go_translations = get_post_meta( $post->ID, 'go_translations' ); // 'en' => 12, fr => 13

    	foreach ( $lang_enabled as $lang ) {
    		
    		if ( !empty($go_translations[$lang]) ) continue;

            $post_content = go_translate_tag_adapter ( $post->post_content, $lang );
            $post_title = go_translate_tag_adapter ( $post->post_title, $lang );

            if ( $post_content && $post_title ) {

            	$cat_id = get_term_by('slug', $lang, 'category');
            	$cat_id = $cat_id->term_id;

            	$post_data_for_insert = array(
                  'comment_status' => 'open', // 'closed' означает, что комментарии закрыты.
                  'post_category' => array($cat_id),
                  'post_content' => $post_content,
                  'post_status' => $post_status? $post_status : 'draft',
                  'post_title' => $post_title,
                  'post_type' => 'post' );

                $inserted_post_id = wp_insert_post( $post_data_for_insert );
                $go_translations[$lang] = $inserted_post_id;
                update_post_meta( $post->ID, 'go_translations', $go_translations );
                add_post_meta( $inserted_post_id, 'source_lang', $post->ID, true );
            } else return 'google_translate_not_responding';
    	}
    }
}

function delete_translated_posts ( $lang=false ) {

    $russian_posts = get_posts( array(
	    'offset'            =>    0,
	    'category'          =>    'ru',
	    'post_type'         =>    'post') );

	foreach ($russian_posts as $post) { setup_postdata( $post );

		$go_translations = get_post_meta( $post->ID, 'go_translations' ); // 'en' => 12, fr => 13
		
		foreach ( $go_translations as $translation_lang_code => $translation_id ) {
			
			if ( !$lang || $lang=='all' )
				wp_delete_post( $translation_id, true );
			elseif ( $lang == $translation_lang_code )
				wp_delete_post( $translation_id, true );
		}
	}
}