<?php
/**
 * allows to translate tags img: alt, title and exclude tags code from translation
 */
function go_translate_tag_adapter ( $text, $lang_code ) {
return $lang_code . "-" . $text;
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

/**
 * translate all content
 */
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