<?php

// Set default timezone
date_default_timezone_set('Europe/Berlin');

// Define currencies that should *not* be included in the list
$excludedCurrencies = ['bits', 'sats'];

require_once '../lib/cache.php';

// Fetch JSON data from a file and decode it
function fetchJson($filename)
{
    return json_decode(file_get_contents($filename), true);
}

function fetchCache(string $key, string $url, FileCache $cache)
{
    return $cache->getOrSet($key, function () use ($url) {
        return makeApiRequest($url);
    }, 60);
}

// Make an API request and return the JSON response
function makeApiRequest($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($json, true);
    }

    return null;
}

// Get CoinGecko key URL parameter
function getCoinGeckoApiUrl($path, $params = [])
{
    $secrets = require '../secrets.php';
    $demo = $secrets['coingecko_key_is_demo'];

    if ($secrets['use_api_key'] === true) {
        $key = $secrets['coingecko_api_key'];
        $paramName = $demo ? 'x_cg_demo_api_key' : 'x_cg_pro_api_key';
        $params[$paramName] = $key;
    }

    $baseUrl = $demo ? "https://api.coingecko.com/api/v3/" : "https://pro-api.coingecko.com/api/v3/";

    $url = $baseUrl . $path;

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
}

$currentTime = time();

// Fetch list of available currencies from CoinGecko API
// Available currencies are cached for 24 hours
function fetchAvailableCurrencies()
{
    $cacheFile = dirname(__DIR__) . '/storage/coingecko-currencies.json';
    $cacheTime = 86400;

    // Return cached data if it exists and is less than 24 hours old
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        return fetchJson($cacheFile);
    }

    $apiUrl = getCoinGeckoApiUrl('simple/supported_vs_currencies');
    $data = makeApiRequest($apiUrl);

    if ($data) {
        file_put_contents($cacheFile, json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    return $data;
}

// Fetch currency data from CoinGecko API
function fetchCurrencyData($currencies)
{
    $cache = new FileCache(dirname(__DIR__) . '/storage/cache');
    $apiUrl = getCoinGeckoApiUrl('simple/price', ['ids' => 'monero', 'vs_currencies' => implode(',', array_map('strtolower', $currencies))]);
    return fetchCache('currency_data', $apiUrl, $cache);
}

$currencyFile = dirname(__DIR__) . '/storage/coingecko.json';
$originalFile = dirname(__DIR__) . '/storage/coingecko-original.json';

// Function to process currency data
function processCurrencyData($availableCurrencies, $previousData, $currentTime, $excludedCurrencies)
{
    // Remove excluded currencies
    $availableCurrencies = array_diff($availableCurrencies, $excludedCurrencies);
    $currencies = array_map('strtoupper', $availableCurrencies);

    // Fetch the latest data from CoinGecko API
    $fetchedData = fetchCurrencyData($currencies);

    if ($fetchedData) {
        $moneroData = $fetchedData['monero'];
        $newData = ['time' => $currentTime];

        // Update the data for each currency
        foreach ($currencies as $currency) {
            $currencyLower = strtolower($currency);
            $newData[$currencyLower] = [
                'lastValue' => $moneroData[$currencyLower] ?? $previousData[$currencyLower]['lastValue'] ?? null,
                'lastDate' => $currentTime
            ];
        }

        return $newData;
    }

    return null;
}

$previousData = file_exists($currencyFile) ? fetchJson($currencyFile) : ["time" => 0];
$output = $previousData;

// Check if five seconds have passed since the last update
if (($currentTime - $previousData['time']) >= 5) {
    $availableCurrencies = fetchAvailableCurrencies();
    if ($availableCurrencies !== null) {
        $output = processCurrencyData($availableCurrencies, $previousData, $currentTime, $excludedCurrencies);

        // Save the data if the API call was successful
        if ($output !== null) {
            file_put_contents($currencyFile, json_encode($output, JSON_PRETTY_PRINT));
            file_put_contents($originalFile, json_encode($output, JSON_PRETTY_PRINT));
        }
    }
}

// Output the data
header('Content-Type: application/json');
echo json_encode($output, JSON_PRETTY_PRINT);
