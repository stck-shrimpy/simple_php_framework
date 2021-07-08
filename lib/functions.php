<?php

function h($str, $flags = ENT_QUOTES, $encoding = 'UTF-8') {
    return htmlspecialchars($str, $flags, $encoding);
}

function is_empty($var) {
    return ($var === [] || $var === '' || $var === null || $var === false);
}

function get_protocol() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
}

function get_url(string $uri = '', array $params = []) {
    return get_protocol() . '://' . $_SERVER['HTTP_HOST'] . get_uri($uri, $params);
}

function get_uri(string $uri, array $params = []) {
    if (!empty($params)) {
        $glue = (strpos($uri, '?') === false) ? '?' : '&';
        $uri .= $glue . http_build_query($params, '', '&');
    }

    if (!defined('BASE_PATH')) {
        return $uri;
    }

    return BASE_PATH . '/' . $uri;
}

function get_uniq_string(int $length = 16) {
    $chars   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charLen = strlen($chars);

    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $chars[mt_rand(0, $charLen - 1)];
    }

    return $string;
}

function format_byte(int $byte, int $decimal = 1) {
    if ($byte >= pow(1024, 4)) {
        return ((string) number_format(($byte / pow(1024, 4)), $decimal)) . 'TB';
    } elseif ($byte >= pow(1024, 3)) {
        return ((string) number_format(($byte / pow(1024, 3)), $decimal)) . 'GB';
    } elseif ($byte >= pow(1024, 2)) {
        return ((string) number_format(($byte / pow(1024, 2)), $decimal)) . 'MB';
    } elseif ($byte >= pow(1024, 1)) {
        return ((string) number_format(($byte / pow(1024, 1)), $decimal)) . 'KB';
    }

    return ((string) $byte) . 'B';
}

function d() {
    echo '<pre style="background: #07031a; color: #42e6a4; ' .
         'border: 1px solid #ccc; margin: 5px; padding: 10px;">';

    foreach (func_get_args() as $value) {
      var_dump($value);
    }

    echo '</pre>';
}
