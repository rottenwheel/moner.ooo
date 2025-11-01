<?php
date_default_timezone_set('Europe/Berlin');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$COINGECKO_JSON_PATH = dirname(__DIR__) . '/storage/coingecko.json';
$HAVENO_JSON_PATH = dirname(__DIR__) . '/storage/haveno.json';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parentUrl = dirname($currentUrl);

// Configuration file
$config = [];
if (file_exists(dirname(__DIR__) . '/config.php')) {
    $config = require_once '../config.php';
}

// Handle data source selection from URL parameter
if (isset($_GET['data_source']) && in_array($_GET['data_source'], ['coingecko', 'haveno'])) {
    $data_source = $_GET['data_source'];
} else {
    $data_source = isset($config['data_source']) ? $config['data_source'] : 'coingecko';
}

// Get currency data from JSON based on data source
if ($data_source === 'haveno') {
    if (!file_exists($HAVENO_JSON_PATH)) {
        // Special case: First run.
        exec('php haveno.php');
    }
    $api_data = json_decode(file_get_contents($HAVENO_JSON_PATH), true);
    $source_name = 'Haveno.markets';
    $source_url = 'https://haveno.markets';
} else {
    // Default to CoinGecko
    if (!file_exists($COINGECKO_JSON_PATH)) {
        // Special case: First run.
        exec('php coingecko.php');
    }
    $api_data = json_decode(file_get_contents($COINGECKO_JSON_PATH), true);
    $source_name = 'CoinGecko';
    $source_url = 'https://www.coingecko.com/en/coins/monero';
}

$display_servers_guru = isset($config['servers_guru']) && $config['servers_guru'] === true;
$attribution = isset($config['attribution']) ? $config['attribution'] : '';
$preferred_currencies = isset($config['preferred_currencies']) ? $config['preferred_currencies'] : [];
$github_url = isset($config['github_url']) ? $config['github_url'] : 'https://github.com/rottenwheel/moner.ooo/';
$footer_html = isset($config['footer_html']) ? $config['footer_html'] : '';

// Extract the keys
$currencies = array_map('strtoupper', array_keys($api_data));

// Fetch the time of the last API data update
$time = date("H:i:s", $api_data['time']);
unset($currencies[array_search('TIME', $currencies)]);

// Order preferred currencies to the top
foreach (array_reverse($preferred_currencies) as $currency) {
    $currency = strtoupper($currency);
    if (($key = array_search($currency, $currencies)) !== false) {
        unset($currencies[$key]);
        array_unshift($currencies, $currency);
    }
}

// Check if the selected currency is available in the current data source
$xmr_in = isset($_GET["in"]) ? strtoupper(string: htmlspecialchars($_GET["in"])) : 'EUR';

if (!in_array($xmr_in, $currencies)) {
    // If not available, fall back to a default currency that should be available in both sources
    $fallback_currencies = ['USD', 'BTC', 'EUR'];
    foreach ($fallback_currencies as $fallback) {
        if (in_array($fallback, $currencies)) {
            $xmr_in = $fallback;
            break;
        }
    }
    // If none of the fallbacks are available, just use the first available currency
    if (!in_array($xmr_in, $currencies)) {
        $xmr_in = $currencies[0];
    }
}

// Populate exchange rates
$exchangeRates = [];
foreach ($currencies as $currency) {
    $currencyLower = strtolower($currency);
    if (isset($api_data[$currencyLower]) && isset($api_data[$currencyLower]['lastValue'])) {
        $exchangeRates[$currency] = $api_data[$currencyLower]['lastValue'];
    }
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

// Include the data source in the info text
$info = str_replace('CoinGecko', $source_name, $info);
$info = str_replace('https://www.coingecko.com/en/coins/monero', $source_url, $info);

// Output the HTML
require_once '../templates/index.php';
