<?php 
/*
Plugin Name: Go_Trans
Plugin URI: none
Description: обходимся без переводов WPML
Version: 0.1
Author: Andrej Serdjuk
Author URI: http://vk.com/lincoln6eco
*/

/*
write trigger of post deleting
- delete postmeta of translation and update postmeta of source
*/
require_once("go_form.php");
require_once("lib_go_trans.php");

add_filter( 'wp_title', 'getDomainTitle', 100, 1);
add_filter( 'the_permalink', 'setDomainPermalink' );

add_action( 'admin_menu', 'register_go_trans_menu_page' );
add_action( 'add_meta_boxes', 'add_go_translate_metabox' );


if ( !strstr($_SERVER["REQUEST_URI"], 'wp-admin') )
    add_action( 'pre_get_posts', 'setLoadingCategory' );


function add_go_translate_metabox () {

    $post_categories = wp_get_post_categories( (int) $_GET['post'], array('fields' => 'names') );
    if ( false === array_search('ru', $post_categories) )
        return false;

    add_meta_box(
        'go_tr',
        'Опции перевода',
        'html_go_translate_metabox',
        'post',
        'normal',
        'low'
    );
}

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
    $lang_set = '{"az":"азербайджанский","sq":"албанский","en":"английский","ar":"арабский","hy":"армянский","af":"африкаанс","eu":"баскский","be":"белорусский","bn":"бенгальский","bg":"болгарский","cy":"валлийский","hu":"венгерский","vi":"вьетнамский","gl":"галисийский","nl":"голландский","el":"греческий","ka":"грузинский","gu":"гуджарати","da":"датский","iw":"иврит","yi":"идиш","id":"индонезийский","ga":"ирландский","is":"исландский","es":"испанский","it":"итальянский","kn":"каннада","ca":"каталанский","zh-TW":"китайский (традиционный)","zh-CN":"китайский (упрощенный)","ko":"корейский","ht":"креольский (Гаити)","lo":"лаосский","la":"латынь","lv":"латышский","lt":"литовский","mk":"македонский","ms":"малайский","mt":"мальтийский","de":"немецкий","no":"норвежский","fa":"персидский","pl":"польский","pt":"португальский","ro":"румынский","ru":"русский","sr":"сербский","sk":"словацкий","sl":"словенский","sw":"суахили","tl":"тагальский","th":"тайский","ta":"тамильский","te":"телугу","tr":"турецкий","uk":"украинский","ur":"урду","fi":"финский","fr":"французский","hi":"хинди","hr":"хорватский","cs":"чешский","sv":"шведский","eo":"эсперанто","et":"эстонский","ja":"японский"}';
    $lang_set = json_decode($lang_set, true);

    add_option( 'domain_lang', array('rus'=>'ru'));
    add_option( 'lang_enabled', array('ru'));
    delete_option('lang_set');
    add_option( 'lang_set', $lang_set );

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

    if ( !empty($_POST['update_title_translations']) ) {
        setDomainTitle();
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

    if ( !$post_id ) {

        $cat_id = get_term_by('slug', 'ru', 'category');
        $cat_id = $cat_id->term_id;

        $russian_posts = get_posts( array(
        'offset'       =>    0,
        'numberposts'  =>   -1,
        'category'     =>    $cat_id,
        'post_type'    =>    'post') );
    } else
        $russian_posts = array( get_post( $post_id ) );

        // var_dump($russian_posts); exit;
    $lang_enabled = get_option('lang_enabled');

    foreach ( $russian_posts as $post ) { setup_postdata($post);
        
        $go_translations = get_post_meta( $post->ID, 'go_translations' ); // 'en' => 12, fr => 13
        $go_translations = $go_translations[0]; // key 0 contains serialized array - this idiotism by wp creators

        foreach ( $lang_enabled as $lang ) {
            
            if ( !empty($go_translations[$lang]) || $lang=='ru' ) continue;

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

function delete_translated_posts ( $lang=false, $ru_post_id=false, $post_id=false ) {

    if ( $post_id && $ru_post_id ) {

        $go_translations = get_post_meta( $ru_post_id, 'go_translations' ); // 'en' => 12, fr => 13
        $go_translations = $go_translations[0]; // key 0 contains serialized array - this idiotism by wp creators
        
        if ( $post_id!='all' ) {

            wp_delete_post( $post_id, true );
            delete_post_meta( $post_id, 'source_lang' );
            $post_lang = array_search($post_id, $go_translations);
           
            if ( $post_lang ) {
                unset( $go_translations[$post_lang] );
                update_post_meta( $ru_post_id, 'go_translations', $go_translations );
            }
        } else {
            foreach ( $go_translations as $tr_lang_code => $tr_id ) {
                wp_delete_post( $tr_id, true );
                delete_post_meta( $tr_id, 'source_lang' );
            }
            delete_post_meta( $ru_post_id, 'go_translations' );
        }
    } else {

        $ru_cat_id = get_category_by_slug('ru');

            $russian_posts = get_posts( array(
                'offset'            =>    0,
                'numberposts'       =>   -1,
                'category'          =>    $ru_cat_id->term_id,
                'post_type'         =>    'post',
                'suppress_filters'  =>    true ) );

        foreach ($russian_posts as $post) { setup_postdata( $post );

            $go_translations = get_post_meta( $post->ID, 'go_translations' ); // 'en' => 12, fr => 13
            $go_translations = $go_translations[0]; // key 0 contains serialized array - this idiotism by wp creators
            
            if ( !empty($go_translations) ) {

                foreach ( $go_translations as $translation_lang_code => $translation_id ) {

                    $translation_id = (int) $translation_id; // oh fucking string was here
                    if ( !$lang || $lang=='all' ) {
                        wp_delete_post( $translation_id, true );
                        unset($go_translations[$translation_lang_code]);
                    } elseif ( $lang == $translation_lang_code ) {
                        wp_delete_post( $translation_id, true );
                        unset($go_translations[$translation_lang_code]);
                    }
                }
                update_post_meta( $post->ID, 'go_translations', $go_translations );
            }
        }
    }
}

function getSubDomain() {
    
    $domain_lang = get_option('domain_lang');
    foreach ($domain_lang as $domain => $lang) {
        if ( strstr( $_SERVER["HTTP_HOST"], $domain ) )
            return $domain;
    }
}

function getCategoryDomain() {
    
    $subDomain = getSubDomain();
    $domain_lang = get_option('domain_lang');
    return get_category_by_slug( $domain_lang[$subDomain] );
}

function setLoadingCategory ( $query ) {

    $category = getCategoryDomain();
    // var_dump($category); exit;
    if ( $category )
        $query->set( 'cat', $category->term_id );
}

function setDomainPermalink ( $permalink ) {

    $domain_lang = get_option('domain_lang');
    foreach ($domain_lang as $domain => $lang) {
        if ( strstr( $_SERVER["HTTP_HOST"], $domain ) ) {

            if ( strstr($permalink, 'www') )
                return str_replace('www', $domain, $permalink);
            else {
                return str_replace("http://", "http://$domain.", $permalink);
            }
        }
    }    //   "/(?<=\/\/)[^\.]*(?=\.[^\.]*\.[^\.]*)/" subdomain or www (maybe for future)
}

function setDomainTitle () {
    
    $source_title = get_bloginfo('name');
    $domain_lang = get_option('domain_lang');
    add_option('go_domain_titles', '');
    $domain_titles = array();
    foreach ($domain_lang as $domain => $lang) {
        $domain_titles[$domain] = go_translate( $source_title, $lang );
    }
    update_option('go_domain_titles', $domain_titles);
}

function getDomainTitle ( $title ) {

    $go_domain_titles = get_option('go_domain_titles');
    $subdomain = getSubDomain();
    if ( !empty($go_domain_titles[$subdomain]) )
        return $go_domain_titles[$subdomain];
}

function save_canonical_domains ( $post, $subdomain_lang ) {
    
    $canonical_subdomains = get_post_meta($post->ID, 'canonical_subdomains');
    $canonical_subdomains = $canonical_subdomains[0];

    if ( !$canonical_subdomains )
        $canonical_subdomains = array();

    foreach ( $subdomain_lang as $subdomain => $lang ) {
            
        if ( !empty($_POST["canonical-$subdomain"]) && 
            $_POST["canonical-$subdomain"] == 'on' ) {
            if ( false===in_array($subdomain, $canonical_subdomains) ) {
                $canonical_subdomains[] = $subdomain;
            }
        } else {
            $key = array_search($subdomain, $canonical_subdomains);
            if ( false!==$key )
                unset( $canonical_subdomains[$key] );
        }
    }
    update_post_meta($post->ID, 'canonical_subdomains', $canonical_subdomains);
}

function get_canonical_links( $post_id ) {

    $canonical_subdomains = get_post_meta($post_id, 'canonical_subdomains');
    $canonical_subdomains = $canonical_subdomains[0];
    $this_permalink = get_permalink( $post_id );

    if ( !empty($canonical_subdomains) ) {

        foreach ( $canonical_subdomains as $subdomain ) {
            
            // $permalink = 
        }
    }
}