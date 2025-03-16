<?php
date_default_timezone_set('Europe/Berlin');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$COINGECKO_JSON_PATH = dirname(__DIR__) . '/storage/coingecko.json';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parentUrl = dirname($currentUrl);

// Get currency data from JSON
if (!file_exists($COINGECKO_JSON_PATH)) {
    `php coingecko.php`;
    sleep(1);
}

$api_cg = json_decode(file_get_contents($COINGECKO_JSON_PATH), true);

// Configuration file
$config = [];
if (file_exists(dirname(__DIR__) . '/config.php')) {
    $config = require_once '../config.php';
}

$display_servers_guru = isset($config['servers_guru']) && $config['servers_guru'] === true;
$attribution = isset($config['attribution']) ? $config['attribution'] : '';
$preferred_currencies = isset($config['preferred_currencies']) ? $config['preferred_currencies'] : [];
$github_url = isset($config['github_url']) ? $config['github_url'] : 'https://github.com/rottenwheel/moner.ooo/';
$footer_html = isset($config['footer_html']) ? $config['footer_html'] : '';

// Extract the keys
$currencies = array_map('strtoupper', array_keys($api_cg));

// Fetch the time of the last API data update
$time_cg = date("H:i:s", $api_cg['time']);
$time = $time_cg;
unset($currencies[array_search('TIME', $currencies)]);

// Populate exchange rates
$exchangeRates = [];
foreach ($currencies as $currency) {
    $exchangeRates[$currency] = $api_cg[strtolower($currency)]['lastValue'];
}

// Get the primary language from the browser
$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : "en";
$lang = explode(",", $lang)[0];
$lang = explode(";", $lang)[0];
$lang = strtolower($lang);

// Aliases for different Chinese variants
$aliases = [
    'zh' => 'zh-hans',
    'zh-hk' => 'zh-hant',
    'zh-tw' => 'zh-hant',
    'zh-cn' => 'zh-hans',
    'zh-sg' => 'zh-hans',
    'zh-mo' => 'zh-hant',
];

if (isset($aliases[$lang])) {
    $lang = $aliases[$lang];
}

// Load the language files
// Take English as a base, then overwrite with the browser language
// E.g.: First load en.php, then de.php, then de-at.php

$language_code = explode('-', $lang)[0];
$language_files = ["en", $language_code, $lang];

foreach ($language_files as $language_file) {
    if (file_exists('../lang/' . $language_file . '.php')) {
        require_once '../lang/' . $language_file . '.php';
    }
}

// Calculation through GET parameters

$xmr_in = isset($_GET["in"]) ? strtoupper(htmlspecialchars($_GET["in"])) : 'USD';
$xmr_amount = isset($_GET["xmr"]) ? floatval($_GET["xmr"]) : 1;
$fiat_amount = isset($_GET["fiat"]) ? floatval($_GET["fiat"]) : '';
$conversion_direction = isset($_GET["direction"]) ? intval($_GET["direction"]) : 0;

if ($conversion_direction == 0) {
    $fiat_value = $xmr_amount * $exchangeRates[$xmr_in];
    $xmr_value = $xmr_amount;
} else {
    $xmr_value = $fiat_amount / $exchangeRates[$xmr_in];
    $fiat_value = $fiat_amount;
}

$fiat_value = number_format($fiat_value, ($xmr_in == 'BTC' || $xmr_in == 'LTC' || $xmr_in == 'ETH' || $xmr_in == 'XAG' || $xmr_in == 'XAU') ? 8 : 2);
$xmr_value = number_format($xmr_value, 12);

$fiat_value = strtr($fiat_value, ",", " ");

// Order preferred currencies to the top
foreach (array_reverse($preferred_currencies) as $currency) {
    $currency = strtoupper($currency);
    if (($key = array_search($currency, $currencies)) !== false) {
        unset($currencies[$key]);
        array_unshift($currencies, $currency);
    }
}

// Output the HTML
require_once '../templates/index.php';
