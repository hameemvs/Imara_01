<?php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'puttalambookzone');

define('BASE_PATH', __DIR__);
define('CURRENT_DOMAIN', current_domain());
define('SLOT_DURATION', 'PT60M');

// Set the timezone to your desired timezone
date_default_timezone_set('Asia/Colombo');

function current_domain()
{
    return protocol() . $_SERVER['HTTP_HOST'] . '/Puttalalm-Book-Zone';
}

function currentUrl()
{
    return current_domain() . $_SERVER['REQUEST_URI'];
}

function protocol()
{
    return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
}

function asset($src)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $src = $domain . '/' . trim($src, '/ ');
    return $src;
}

function url($url)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $url = $domain . '/' . trim($url, '/ ');
    return $url;
}

//For debug
function dd($data, $comment = '')
{
    print('<pre>');
    print($comment);
    print('<br>');

    print_r($data);
    print('</pre>');

    die;
}

//For debug print
function pr($data, $comment = '')
{
    print('<pre>');
    print($comment);
    print('<br>');

    print_r($data);
    print('</pre>');
}

function getDays()
{
    return [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
    ];
}

// Function to generate a random string
function generateRandomString($length = 4)
{
    return bin2hex(random_bytes($length / 2));
}
