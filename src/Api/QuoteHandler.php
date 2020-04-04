<?php

namespace Ingelby\Alphavantage\Api;

use Ingelby\Alphavantage\Exceptions\AlphavantageRateLimitException;
use Ingelby\Alphavantage\Exceptions\AlphavantageResponseException;
use Ingelby\Alphavantage\Models\Quote;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\InguzzleHandler;

class QuoteHandler extends AbstractHandler
{
    protected const GLOBAL_QUOTE = 'GLOBAL_QUOTE';

    /**
     * @param string $symbol
     * @return Quote
     * @throws AlphavantageResponseException
     * @throws AlphavantageRateLimitException
     */
    public function getGlobalQuote(string $symbol)
    {
        $response = $this->query(
            static::GLOBAL_QUOTE,
            [
                'symbol' => $symbol,
            ]
        );

        if (!array_key_exists('Global Quote', $response)) {
            throw new AlphavantageResponseException(HttpStatus::BAD_REQUEST, 'No response');
        }
        $response =  $response['Global Quote'];

        return new Quote(
            [
                'symbol'           => $response['01. symbol'] ?? null,
                'open'             => $response['02. open'] ?? null,
                'high'             => $response['03. high'] ?? null,
                'low'              => $response['04. low'] ?? null,
                'price'            => $response['05. price'] ?? null,
                'volume'           => $response['06. volume'] ?? null,
                'latestTradingDay' => $response['07. latest trading day'] ?? null,
                'previousClose'    => $response['08. previous close'] ?? null,
                'change'           => $response['09. change'] ?? null,
                'changePercent'    => $response['10. change percent'] ?? null,
            ]
        );
    }
}









