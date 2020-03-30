<?php

namespace Ingelby\Alphavantage\Api;

use ingelby\toolbox\services\InguzzleHandler;

class AbstractHandler extends InguzzleHandler
{
    public function __construct($baseUrl, $uriPrefix = '', callable $clientErrorResponseCallback = null, callable $serverErrorResponseCallback = null)
    {
        parent::__construct($baseUrl, $uriPrefix, $clientErrorResponseCallback, $serverErrorResponseCallback);
    }
}
