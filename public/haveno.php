<?php

// Set default timezone
date_default_timezone_set('Europe/Berlin');

// Define the base URL for the Haveno API
$haveno_api_base_url = "https://haveno.markets/api/v1";
$haveno_json_path = dirname(__DIR__) . '/storage/haveno.json';

// Fetch the previously stored data
$previousData = file_exists($haveno_json_path) ? json_decode(file_get_contents($haveno_json_path), true) : ['time' => 0];
$output = $previousData;

$currentTime = time();

$updated = false;

// Check if five seconds have passed since the last update
if (($currentTime - $previousData['time']) >= 5 && getenv('MONEROOO_TICKER_UPDATE') == '1') {
    // Fetch the ticker data from Haveno API
    $tickerUrl = $haveno_api_base_url . "/tickers";

    $tickerCh = curl_init($tickerUrl);
    curl_setopt($tickerCh, CURLOPT_RETURNTRANSFER, true);
    $tickerJson = curl_exec($tickerCh);

    $tickerHttpCode = curl_getinfo($tickerCh, CURLINFO_HTTP_CODE);
    curl_close($tickerCh);

    if ($tickerHttpCode == 200) {
        $tickers = json_decode($tickerJson, true);

        // Initialize new data array
        $newData = ['time' => $currentTime];

        // Process each ticker to get XMR exchange rates
        foreach ($tickers as $ticker) {
            $pair = strtolower($ticker['pair']);
            if (strpos($pair, 'xmr_') === 0) {
                // Extract the currency from market pair (e.g., xmr_usd -> usd)
                $currency = substr($pair, 4);

                // Store the last price as the exchange rate
                $newData[$currency] = [
                    'lastValue' => floatval($ticker['last_price']),
                    'lastDate' => $currentTime
                ];
            } elseif (strpos($pair, '_xmr') !== false) {
                // Extract the currency from market pair (e.g., usd_xmr -> usd)
                $currency = substr($pair, 0, strpos($pair, '_xmr'));

                // Store the last price as the exchange rate
                $newData[$currency] = [
                    'lastValue' => 1 / floatval($ticker['last_price']), // Inverse for XMR to fiat
                    'lastDate' => $currentTime
                ];
            }
        }

        // Save the new data to JSON files
        file_put_contents($haveno_json_path, json_encode($newData, JSON_PRETTY_PRINT));
        file_put_contents($haveno_json_path . ".bak", json_encode($tickers, JSON_PRETTY_PRINT));

        $output = $newData;
        $updated = true;
    }
}

if (getenv('MONEROOO_TICKER_UPDATE') == '1') {
    if ($updated)
        echo 'Updated ticker at ' . $currentTime;
    else
        echo 'Ticker unchanged.';
} else {
    // Output the data
    header('Content-Type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT);
}
