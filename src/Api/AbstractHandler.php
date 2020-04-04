<?php

namespace Ingelby\Alphavantage\Api;

use common\helpers\LoggingHelper;
use Ingelby\Alphavantage\Exceptions\AlphavantageRateLimitException;
use Ingelby\Alphavantage\Exceptions\AlphavantageResponseException;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\inguzzle\exceptions\InguzzleClientException;
use ingelby\toolbox\services\inguzzle\exceptions\InguzzleInternalServerException;
use ingelby\toolbox\services\inguzzle\exceptions\InguzzleServerException;
use ingelby\toolbox\services\inguzzle\InguzzleHandler;
use yii\caching\TagDependency;
use yii\helpers\Json;

class AbstractHandler extends InguzzleHandler
{
    protected const DEFAULT_URL = 'https://www.alphavantage.co';
    protected const DEFAULT_ERROR_MESSAGE = 'Error Message';
    protected const DEFAULT_NOTE_MESSAGE = 'Note';
    protected const CACHE_KEY = 'ALPHAVANTAGE_';
    protected const CACHE_TAG_DEPENDANCY = 'ALPHAVANTAGE';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var int
     */
    protected $cacheTimeout = 600;

    /**
     * AbstractHandler constructor.
     *
     * @param string      $apiKey
     * @param string|null $baseUrl
     */
    public function __construct(string $apiKey, $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;

        if (null === $this->baseUrl) {
            $this->baseUrl = static::DEFAULT_URL;
        }

        parent::__construct($this->baseUrl);
    }

    /**
     * @param string $function
     * @param array  $headers
     * @throws AlphavantageResponseException
     * @throws AlphavantageRateLimitException
     */
    public function query(string $function, array $headers)
    {
        $standardHeaders = [
            'apikey'   => $this->apiKey,
            'function' => $function,
        ];
        $finalHeaders = array_merge($standardHeaders, $headers);

        $cacheKey = static::CACHE_KEY . $function . md5(Json::encode($finalHeaders));

        return \Yii::$app->cache->getOrSet(
            $cacheKey,
            function () use ($finalHeaders) {
                try {
                    $response = $this->get('query', $finalHeaders);

                    if (array_key_exists(static::DEFAULT_ERROR_MESSAGE, $response)) {
                        \Yii::error($response[static::DEFAULT_ERROR_MESSAGE]);
                        throw new AlphavantageResponseException(
                            HttpStatus::BAD_REQUEST,
                            $response[static::DEFAULT_ERROR_MESSAGE]
                        );
                    }
                    
                    if (array_key_exists(static::DEFAULT_NOTE_MESSAGE, $response)) {
                        \Yii::error($response[static::DEFAULT_NOTE_MESSAGE]);
                        throw new AlphavantageRateLimitException(
                            HttpStatus::BAD_REQUEST,
                            'Rate limit reached'
                        );
                    }

                    return $response;
                } catch (InguzzleClientException | InguzzleInternalServerException | InguzzleServerException $e) {
                    throw new AlphavantageResponseException($e->statusCode, 'Error contacting Alphavantage', 0, $e);
                }
            },
            $this->cacheTimeout,
            new TagDependency(['tags' => static::CACHE_TAG_DEPENDANCY])
        );
    }

    /**
     * @param int $cahceTimeout
     */
    public function setCacheTimeout(int $cahceTimeout)
    {
        $this->cacheTimeout = $cahceTimeout;
    }
}
