<?php

function get_top_coins() {
    $url = API_ENDPOINT . '/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=100&page=1&sparkline=false&price_change_percentage=24h';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data;
}

function get_coin_price($coin_id, $exchange_id) {
    $url = API_ENDPOINT . '/simple/price?ids=' . $coin_id . '&vs_currencies=usd&include_24hr_change=true&include_last_updated_at=true';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data[$coin_id]['usd'];
}

function check_arbitrage_opportunities($coin) {
    $coin_id = $coin['id'];
    $coin_name = $coin['name'];

    $opportunities = array();

    foreach ($coin['tickers'] as $ticker1) {
        $exchange1 = $ticker1['market']['name'];
        $price1 = $ticker1['converted_last']['usd'] * USD_EXCHANGE_RATE;

        foreach ($coin['tickers'] as $ticker2) {
            $exchange2 = $ticker2['market']['name'];
            $price2 = $ticker2['converted_last']['usd'] * USD_EXCHANGE_RATE;

            if ($exchange1 != $exchange2 && $price1 > 0 && $price2 > 0) {
                $spread = abs(($price1 - $price2) / (($price1 + $price2) / 2));
                if ($spread >= MIN_SPREAD_PERCENTAGE / 100) {
                    $opportunities[] = array(
                        'coin' => $coin_name,
                        'exchange1' => $exchange1,
                        'price1' => $price1,
                        'exchange2' => $exchange2,
                        'price2' => $price2,
                        'spread' => $spread
                    );
                }
            }
        }
    }

    return $opportunities;
}
