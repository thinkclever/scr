<?php

function custom_hash($user, $realm, $password) {
    $password_a1 = md5($user. ':' .$realm. ':' .$password);
    return $password_a1;
}

function number_filter($in) {
    $out = preg_replace('/[^0-9\.\-]/', '', $in);
    return $out;
}

function date_filter($in) {
    $out = preg_replace('/[^0-9\-\s\.\:]/', '', $in);
    return $out;
}

function alpha_filter($in) {
    $out = preg_replace('/[^0-9A-Za-z_\-\=]/', '', $in);
    return $out;
}

function broad_filter($str) {
    $str = str_replace("'", '´', $str);
    $str = str_replace('(', '&#40;', $str);
    $str = str_replace(')', '&#41;', $str);
    $str = preg_replace('~[^0-9A-Za-z_\-\=\s\.,;#&@´/\:\n\*\?\!\"]~', '', $str);
    return $str;
}

function email_filter($str) {
    $str = preg_replace('~[^0-9A-Za-z_\-\.@"]~', '', $str);
    return $str;
}

function purify_everything($str = '') {
    $str = trim($str);
    $str = strip_tags($str);
    $str = str_replace('"', '&#34;', $str);
    $str = str_replace("'", '&#39;', $str);
    $str = str_replace('(', '&#40;', $str);
    $str = str_replace(')', '&#41;', $str);
    $str = preg_replace('~[^0-9A-Za-z_\-\=\s\.,;#&@/\:\n\*\?\!]~', '', $str);
    return $str;
}

if( ! function_exists('secure_site_url') ) {
    function secure_site_url($uri = '') {
        $CI =& get_instance();

        if (is_array($uri)) {
            if (!$CI->config->item('enable_query_strings')) {
                $uri = implode('/', $uri);
                $uri_string = trim($uri, '/');
            }
            else {
                $i = 0;
                $uri_string = '';
                foreach ($uri as $key => $val) {
                    $prefix = ($i == 0) ? '' : '&';
                    $uri_string .= $prefix.$key.'='.$val;
                    $i++;
                }
            }
        }
        else {
            $uri_string = trim($uri, '/');
        }

        if ($uri_string == '') {
            return $CI->config->slash_item('secure_base_url').$CI->config->item('index_page');
        }

        if ($CI->config->item('enable_query_strings')) {
            return $CI->config->slash_item('secure_base_url').$CI->config->item('index_page').'?'.$uri_string;
        }

        $suffix = (!$CI->config->item('url_suffix')) ? '' : $CI->config->item('url_suffix');
        return $CI->config->slash_item('secure_base_url').$CI->config->slash_item('index_page').$uri_string.$suffix;
    }
}

if ( ! function_exists('secure_redirect')) {
    function secure_redirect($uri = '', $method = 'location', $http_response_code = 302) {
        $suri = secure_site_url($uri);
        switch($method) {
            case 'refresh' : header("Refresh:0;url=".$suri);
                break;
            default : header("Location: ".$suri, TRUE, $http_response_code);
                break;
        }
        exit;
    }
}

if ( ! function_exists('hex_pseudo_bytes')) {
    function hex_pseudo_bytes($l = 8) {
        if (function_exists('openssl_random_pseudo_bytes') && version_compare(PHP_VERSION, '5.3.4', '>=')) {
            $tmp = unpack('H*', openssl_random_pseudo_bytes($l/2));
            return array_shift($tmp);
        }
        else if (defined('MCRYPT_DEV_URANDOM') && ($seed = mcrypt_create_iv($l/2, MCRYPT_DEV_URANDOM)) !== FALSE) {
            $tmp = unpack('H*', $seed);
            return array_shift($tmp);
        }
        else {
            $tmp = '0123456789abcdef';
            $l -= 1; $r = $tmp{mt_rand(0,15)};
            while ($l-->0) { $r.=$tmp{mt_rand(0,15)}; }
            return $r;
        }
    }
}