<?php

require_once('arbitrage/config.php');
require_once('arbitrage/functions.php');

// Get the top 100 coins from CoinGecko API
$coins_data = get_top_coins();

// Check for arbitrage opportunities for each coin
$found_opportunities = false;
foreach ($coins_data as $coin) {
    $opportunities = check_arbitrage_opportunities($coin);
    if (!empty($opportunities)) {
        $found_opportunities = true;
        foreach ($opportunities as $opportunity) {
            echo '<h2>Arbitrage Opportunity Found</h2>';
            echo '<p>Coin: ' . $opportunity['coin'] . '</p>';
            echo '<p>Exchange 1: ' . $opportunity['exchange1'] . ', Price 1: $' . number_format($opportunity['price1'], 4) . '</p>';
            echo '<p>Exchange 2: ' . $opportunity['exchange2'] . ', Price 2: $' . number_format($opportunity['price2'], 4) . '</p>';
            echo '<p>Spread: ' . number_format($opportunity['spread'] * 100, 2) . '%</p>';
        }
    } else {
        echo '<p>No arbitrage opportunities found for ' . $coin['name'] . '</p>';
    }
    ob_flush();
    flush();
    sleep(2); // Wait 2 seconds between checking each coin
}

// Notify if no arbitrage opportunities were found
if (!$found_opportunities) {
    echo '<p>No arbitrage opportunities found.</p>';
}
