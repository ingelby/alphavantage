<?php


namespace Ingelby\Alphavantage\Helpers;


use Ingelby\Alphavantage\Models\TimeSeries;

class ChartJsHelper
{

    /**
     * @param TimeSeries[] $timeSeries
     * @return [][];
     */
    public static function mapTimeSeriesPercent(array $timeSeries, float $openingValue)
    {
        $mappedValues = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($timeSeries as $series) {
            $value = round($series->open - $openingValue, 3);
            $mappedValues['data'][] = $value;
            $mappedValues['labels'][] = $value;
        }
        return array_reverse($mappedValues);
    }

}