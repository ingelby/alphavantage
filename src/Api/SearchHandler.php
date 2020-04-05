<?php

namespace Ingelby\Alphavantage\Api;

use Ingelby\Alphavantage\Exceptions\AlphavantageRateLimitException;
use Ingelby\Alphavantage\Exceptions\AlphavantageResponseException;
use Ingelby\Alphavantage\Models\Quote;
use Ingelby\Alphavantage\Models\SearchMatch;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\InguzzleHandler;
use yii\caching\TagDependency;

class SearchHandler extends AbstractHandler
{
    protected const SYMBOL_SEARCH = 'SYMBOL_SEARCH';

    protected const TOP_RESULT_CACHE_KEY_TIMEOUT = 60 * 60 * 24 * 7;
    protected const TOP_RESULT_CACHE_KEY = 'TOP_RESULT_ALPHAVANTAGE_';
    protected const TOP_RESULT_CACHE_TAG_DEPENDANCY = 'TOP_RESULT_ALPHAVANTAGE';

    /**
     * @var array
     */
    protected $extendedSearchResults;

    /**
     * @param string      $apiKey
     * @param array       $extendedSearchResults
     * @param string|null $baseUrl
     */
    public function __construct(string $apiKey, array $extendedSearchResults = [], $baseUrl = null)
    {
        parent::__construct($apiKey, $baseUrl);
        $this->extendedSearchResults = $extendedSearchResults;
    }

    /**
     * @param string $keywords
     * @param int    $resultsLimit
     * @param bool   $orderWithExtendedSearchResults
     * @return SearchMatch[]
     * @throws AlphavantageResponseException
     * @throws AlphavantageRateLimitException
     */
    public function getResults(string $keywords, int $resultsLimit = 10, bool $orderWithExtendedSearchResults = false)
    {
        $response = $this->query(
            static::SYMBOL_SEARCH,
            [
                'keywords' => $keywords,
            ]
        );

        if (!array_key_exists('bestMatches', $response)) {
            throw new AlphavantageResponseException(HttpStatus::BAD_REQUEST, 'No response');
        }

        $searchResults = $response['bestMatches'];

        $searchResults = array_merge($searchResults, $this->extendedSearchResults);

        if (true === $orderWithExtendedSearchResults) {
            usort(
                $searchResults,
                function ($a, $b) {
                    if (!isset($a['9. matchScore'], $b['9. matchScore'])) {
                        return 0;
                    }

                    $aValue = floatval($a['9. matchScore']);
                    $bValue = floatval($b['9. matchScore']);
                    
                    if ($aValue == $bValue) {
                        return 0;
                    }
                    return ($aValue > $bValue) ? -1 : 1;
                }
            );
        }

        $matches = [];

        $currentLimit = 0;

        foreach ($searchResults as $searchResult) {
            if ($currentLimit++ >= $resultsLimit) {
                break;
            }

            $matches[] = new SearchMatch(
                [
                    'symbol'      => $searchResult['1. symbol'] ?? 'Unknown',
                    'name'        => $searchResult['2. name'] ?? 'Unknown',
                    'type'        => $searchResult['3. type'] ?? 'Unknown',
                    'region'      => $searchResult['4. region'] ?? 'Unknown',
                    'marketOpen'  => $searchResult['5. marketOpen'] ?? 'Unknown',
                    'marketClose' => $searchResult['6. marketClose'] ?? 'Unknown',
                    'timezone'    => $searchResult['7. timezone'] ?? 'Unknown',
                    'currency'    => $searchResult['8. currency'] ?? 'USD',
                    'matchScore'  => $searchResult['9. matchScore'] ?? 'Unknown',
                ]
            );
        }

        return $matches;
    }

    /**
     * @param string $symbol
     * @return SearchMatch
     * @throws AlphavantageResponseException
     * @throws AlphavantageRateLimitException
     */
    public function getTopResult(string $symbol)
    {
        $cacheKey = static::TOP_RESULT_CACHE_KEY . $symbol;

        return \Yii::$app->cache->getOrSet(
            $cacheKey,
            function () use ($cacheKey, $symbol) {
                $searchResults = $this->getResults($symbol, 1, true);

                if ([] === $searchResults) {
                    throw new AlphavantageResponseException(
                        HttpStatus::BAD_REQUEST,
                        'No results available for ' . $symbol
                    );
                }

                $companyInformation = current($searchResults);

                return $companyInformation;
            },
            static::TOP_RESULT_CACHE_KEY_TIMEOUT,
            new TagDependency(
                [
                    'tags' => static::TOP_RESULT_CACHE_TAG_DEPENDANCY,
                ]
            )
        );
    }
}
