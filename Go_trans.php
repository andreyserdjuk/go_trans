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

add_action( 'admin_menu', 'register_go_trans_menu_page' );
add_action('trashed_post', 'translation_sync');
add_action('delete_post', 'translation_sync');
add_action('add_meta_boxes', 'add_go_translate_metabox');

function add_go_translate_metabox() {
    add_meta_box(
        'go_tr',
        'Опции перевода',
        'html_go_translate_metabox',
        'post',
        'normal',
        'low'
    );
}

function html_go_translate_metabox( $post ) {
    ?>
    <form></form><!-- without this shit next form tags deleting in output... -->
    <form method="POST">
        <input type="hidden" name="translate_post" value="<?= $post->ID ?>">
        <input type="submit" value="Перевести пост">
        <select name="post_status">
            <option value="draft">Черновик</option>
            <option value="publish">Опубликованные записи</option>
            <option value="future">Будущие</option>
            <option value="private">Приватные</option>
        </select>
    </form>
    <form method="POST">
        <input type="hidden" name="delete_translations" value="<?= $post->ID ?>">
        <input type="submit" value="Удалить переводы">
    </form>

    <?php

    if ( isset($_POST['delete_translations']) )
        delete_all_translated_posts ( $_POST['delete_translations'] );
    
    if ( isset($_POST['translate_post']) )
        translate_post ( $_POST['translate_post'] );
}

function register_go_trans_menu_page () {

    add_menu_page( 'Заполнить пустоту переводами', 'Go_Trans', 'add_users', 'Go_Trans/Go_Trans.php', 'go_trans_admin_menu', plugins_url('Go_Trans/images/icon.png'), 3 );
}

function go_trans_admin_menu() {

    // add scripts, styles...
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

    add_option( 'go_trans_options', json_decode('{ ru-Ru:{enabled:0, lang_code:"ru"} }', true));

    $msg = dispatchPost($lang_set);
    $go_trans_options = get_option('go_trans_options');
    // array(2) { ["ru-Ru"]=> array(2) { ["enabled"]=> int(1) ["lang_domain"]=> string(2) "ru" } ["en-eng"]=> array(2) { ["enabled"]=> int(1) ["lang_domain"]=> string(2) "en" } }
    get_main_form( $go_trans_options, $lang_set, $msg );
}

// old function
function dispatchPost ( $lang_set ) {

    $msg = ">> ";
    // update categories list
    if ( isset($_POST['lang_options']) ) {

        $go_trans_options = json_decode( stripcslashes($_POST['lang_options']), true );
        update_option( 'go_trans_options', $go_trans_options );

        // gather array with absent categories
        $cats_to_delete = array();
        $cats_to_create = array();
        foreach ( $go_trans_options as $domain => $options ) {

            $cat = get_term_by('name', $domain, 'category', ARRAY_A);
            $cats_to_delete[$domain] = $cat['term_id'];
            $domains = array();
            if ( !$cat ) {
                $cats_to_create[$lang_set[$lang_code]] = array( 'lang_code'=>$lang_code, 'domain' => $domain );
            }
        }

        // delete absent in request categories
        $categories_ids = get_all_category_ids(); // get all id's of categories
        foreach ( $categories_ids as $cat_id ) {
            if ( !in_array($cat_id, $cats_to_delete) )
                wp_delete_category($cat_id);
        }
        
        // create absent in database categories
        foreach ( $cats_to_create as $lang_name => $cat ) {
            // wp_create_category($cat_name);
            wp_insert_category(array( 'cat_name' => $cat['domain'],
              'category_description' => $lang_name,
              'category_nicename' => $cat['domain'],
              'taxonomy' => 'category' ) );
        }
        $msg .= "список рубрик обновлен" . PHP_EOL;
    }

    if ( isset($_POST['delete_translations_cache']) ) {
        $wpdb->query("DELETE FROM `wp_go_translations`");
        $msg .= "кеш переводов удален" . PHP_EOL;
    }

    if ( isset($_POST['delete_translated_posts']) ) {
        $translated_posts_field = $_POST['delete_translated_posts'];
        $translated = $wpdb->get_results("SELECT DISTINCT $translated_posts_field FROM `wp_go_translations`", ARRAY_A);
        
        foreach ($translated as $post) {
            wp_delete_post( $post[$translated_posts_field], true );
            $wpdb->query("UPDATE `wp_go_translations` SET $translated_posts_field = NULL WHERE $translated_posts_field = ".$post[$translated_posts_field] );
        }

        $msg .= "$translated_posts_field - посты удалены" . PHP_EOL;
    }

    if ( isset($_POST['delete_all_translated_posts']) ) {
        delete_all_translated_posts();
        $m .= "все переведнные посты удалены" . PHP_EOL;
    }

    if ( isset($_POST['post_status']) ) {
        $go_trans_options = get_option('go_trans_options');
        $task = buildTask( $go_trans_options );
        $ru_cat = get_term_by('slug', 'ru-ru', 'category');
        $ru_cat = $ru_cat->term_id;
        $result = translate_posts( $task, $_POST['post_status'] );

        if ( $result == 'google_translate_not_responding' )
            $m .= "Переводчик гугла не вернул переведенный текст.";
    }
    return $msg;
}

function buildTask ( $go_trans_options ) {
    $task = array();
    foreach ( $go_trans_options as $domain => $opt ) {
        if ( $opt['enabled']==1 )
            $task[$domain] = $opt['lang_domain'];
    }
    return empty($task)? false : $task;
}

function translate_post ( $post_id ) {

    delete_all_translated_posts ( $post_id );
    $lanuages_options = get_option('lanuages_options');
    $languages_keys = get_languages_keys( $lanuages_options );
    global $wpdb;
    $post_status = isset($_POST['post_status'])? $_POST['post_status'] : 'draft';

    if( !empty( $lanuages_options['english']['id'] ) ) $languages_keys['en']      =  $lanuages_options['english']['id'] ;
    if( !empty( $lanuages_options['french']['id']  ) ) $languages_keys['fr']      =  $lanuages_options['french']['id']  ;
    if( !empty( $lanuages_options['chinese']['id'] ) ) $languages_keys['zh-hant'] =  $lanuages_options['chinese']['id'] ;
 
    $lang_table['en'] = 'en_post_id';
    $lang_table['fr'] = 'fr_post_id';
    $lang_table['zh-hant'] = 'cn_post_id';
    
    $post = get_post( $post_id );

    foreach ( $languages_keys as $lang_code => $cat_id ) {

        $post_content = go_translate_tag_adapter ( $post->post_content, $lang_code );
        $post_title = go_translate_tag_adapter ( $post->post_title, $lang_code );
        $lang_table_name = $lang_table[$lang_code];

        $post_data_for_insert = array(
          'comment_status' => 'open', // 'closed' означает, что комментарии закрыты.
          'post_category' => array($cat_id),
          'post_content' => $post_content,
          'post_status' => $post_status,
          'post_title' => $post_title,
          'post_type' => 'post'
        );

        $inserted_post_id = wp_insert_post( $post_data_for_insert, $wp_error );

        // current post field does not exist in table, so we need to insert it
        $test = $wpdb->query("SELECT * FROM `wp_go_translations` WHERE ru_post_id = $post_id");

        if ( empty($test) )
            $wpdb->query("INSERT INTO `wp_go_translations`(ru_post_id, $lang_table_name) VALUES ($post_id, $inserted_post_id)");
        else    // current post fiels already created, so we need to update it
            $wpdb->query("UPDATE `wp_go_translations` SET $lang_table_name = $inserted_post_id WHERE ru_post_id = $post_id");
    }
}

function translation_sync ( $id ) {

    // when post is about to deleted, it is necessary to update table `wp_go_translations`
    $cat = wp_get_post_categories($id);
    $lanuages_options = get_option('lanuages_options');

    $lang_codes['en_post_id'] = $lanuages_options['english']['id'];
    $lang_codes['fr_post_id'] = $lanuages_options['french']['id'];
    $lang_codes['cn_post_id'] = $lanuages_options['chinese']['id'];

    if ( count($cat)==1 ) {

        global $wpdb;
        $cat_id = (int) $cat[0];

        foreach ( $lang_codes as $lang_table_code => $lang_id ) {
            
            if ( $cat_id == $lang_id ) {
                
                $wpdb->query("UPDATE `wp_go_translations` SET $lang_table_code = NULL WHERE $lang_table_code = $id");
            }
        }
    }
}

function delete_all_translated_posts ( $id = false ) {

    global $wpdb;
    if ( $id ) {
        $ids = $wpdb->get_results("SELECT ru_post_id, en_post_id, cn_post_id, fr_post_id FROM `wp_go_translations` WHERE ru_post_id = $id", ARRAY_A);
        $wpdb->query("DELETE FROM `wp_go_translations` WHERE ru_post_id = $id");
    } else {
        $ids = $wpdb->get_results("SELECT ru_post_id, en_post_id, cn_post_id, fr_post_id FROM `wp_go_translations`", ARRAY_A);
        $wpdb->query("DELETE FROM `wp_go_translations`");
    }
    
    if ( !empty($ids) )
    foreach ( $ids as $id ) {
        
        if ( $id['en_post_id'] ) wp_delete_post( $id['en_post_id'], true );
        if ( $id['fr_post_id'] ) wp_delete_post( $id['fr_post_id'], true );
        if ( $id['cn_post_id'] ) wp_delete_post( $id['cn_post_id'], true );
    }
        
}

function go_trans_option_page() {}

/**
 * @param [ array( 'domain' => 'lang_code' ) ]  //     array(2) { ["eng"]=> string(2) "en" ["fr"]=> string(2) "fr" }
 */
function translate_posts ( $task, $post_status, $ru_cat_id ) {

    $russian_posts = get_posts( array(
        'offset'            =>    0,
        'category'          =>    $ru_cat_id,
        'post_type'         =>    'post',
        'post_status'       =>    $post_status) );

    global $wpdb;

    $ru_id = array_search('ru', $task);

    foreach ( $russian_posts as $post ) {  setup_postdata($post);

        foreach ( $task as $domain => $lang_code ) {

            if ( $lang_code=='ru' ) continue;
            $tr_post = $wpdb->get_row($wpdb->prepare("SELECT ru_id FROM `go_translated_posts` WHERE ru_id = %d AND domain = %s", $post->ID,  $domain), ARRAY_A);
            if ( !empty($tr_post['ru_id']) ) continue;

            $post_content = go_translate_tag_adapter ( $post->post_content, $lang_code );
            $post_title = go_translate_tag_adapter ( $post->post_title, $lang_code );

            if ( $post_content && $post_title ) {

                $cat_id = get_term_by('slug', $domain, 'category');
                $cat_id = $cat_id->term_id;

                $post_data_for_insert = array(
                  'comment_status' => 'open', // 'closed' означает, что комментарии закрыты.
                  'post_category' => array($cat_id),
                  'post_content' => $post_content,
                  'post_status' => $post_status,
                  'post_title' => $post_title,
                  'post_type' => 'post'
                );

                $inserted_post_id = wp_insert_post( $post_data_for_insert );

                $wpdb->query($wpdb->prepare("INSERT INTO `go_translated_posts` VALUES(NULL, %d, %d, %s)", $post->ID, $inserted_post_id, $domain));

            } else return 'google_translate_not_responding';
        }
    }
}

function go_translate_tag_adapter ( $text, $lang_code ) {

    preg_match_all( "/(\[[^\]\[]*\])|(<[^>]*>)/", $text, $tags );
    $tag_lib = array();
    $count = 0;
   
    foreach ($tags[0] as $tag) {
        $count++;
        $text = str_replace($tag, '(('.$count.'))', $text);

        preg_match('/title="[^"]*"/', $tag, $title);
        preg_match('/alt="[^"]*"/', $tag, $alt);

        if ( $title ) {

            preg_match('/(?<=title=").*(?=")/', $title[0], $title_text);
            $title_text = go_translate ( $title_text[0], $lang_code );
            $tag = str_replace( $title, 'title="'. $title_text . '"', $tag);
        }

        if ( $alt ) {

            preg_match('/(?<=alt=").*(?=")/', $alt[0], $alt_text);
            $alt_text = go_translate ( $alt_text[0], $lang_code );
            $tag = str_replace( $alt, 'alt="'. $alt_text . '"', $tag);
        }

        $tag_lib[] = $tag;
    }

    $text = go_translate ( $text, $lang_code );

    $leftq = '（（';
    $right = '））';

    str_replace( '（（1））', '1', $text, $count );

    if ( $count==0 ) {
        $leftq = '((';
        $right = '))';
    }

    $code = 0;
    foreach ( $tag_lib as $tag ) {
        $code++;
        $text = str_replace( $leftq.$code.$right, $tag, $text);
    }
    return $text;
}

function go_translate ( $text, $lang_code ) {

    $header = array(
        'POST /translate_a/t HTTP/1.1',
        'Host: translate.google.com.ua',
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
        'Accept-Encoding: gzip, deflate',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
        'Referer: http://translate.google.com.ua/',
        'Pragma: no-cache',
        'Cache-Control: no-cache'
    );

    $data = "client=t&text=" . urlencode($text) . "&hl=ru&sl=ru&tl=" . $lang_code . "&ie=UTF-8&oe=UTF-8&multires=1&ssel=0&tsel=0&sc=1";
    if ( $lang_code=='zh-hant' )
    $data = "client=t&text=" . urlencode($text) . "&hl=ru&sl=ru&tl=zh-CN&ie=UTF-8&oe=UTF-8&multires=1&prev=conf&psl=ru&ptl=en&otf=1&trs=1&it=sel.3608%2Ctgtd.2088&ssel=0&tsel=4";


    $translation = _http( 'http://translate.google.com.ua/translate_a/t', $data, 'POST', $header );
    preg_match('/(?<=\[)\[\[.*(?=,,\"ru)/', $translation, $m);
    $translation = json_decode($m[0], true);

    foreach ( $translation as $part ) {
        $re .= $part[0];
    }

    return $re;
}

/**
 * Get result of http request
 */
function _http( $url, $data = false, $type = 'POST', $header = false ) {
    $ch = curl_init();

    if( isset( $data ) && $type == 'GET' ) {
        $pairs = array();
        foreach ($data as $key => $value) {
            if ( $value )
                $pairs[] = $key . "=" . $value;
        }
        $data = "?" . implode('&', $pairs);
        $url .= $data; 
    }
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
    // curl_setopt( $ch, CURLOPT_HEADER, TRUE);
    curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); 

    if ( $header )
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);

    if( $data && $type == 'POST' ) {
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    }

    $response = curl_exec( $ch );
    curl_close( $ch );
    return $response;
}