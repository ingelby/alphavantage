<?php


namespace Ingelby\Alphavantage\Helpers;


use Ingelby\Alphavantage\Models\SearchMatch;
use Ingelby\Alphavantage\Models\TimeSeries;

class Select2Helper
{

    /**
     * @param SearchMatch[] $searchResults
     * @return
     */
    public static function mapSimple(array $searchResults)
    {

        $mappedValues = [];
        foreach ($searchResults as $searchResult) {
            $mappedValues[] = [
                'id'   => $searchResult->symbol,
                'text' => $searchResult->getFriendlyName(),
            ];
        }
        return $mappedValues;
    }
}
