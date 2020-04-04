<?php


namespace Ingelby\Alphavantage\Models;


use Carbon\Carbon;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class TimeSeriesIntraDay extends TimeSeries
{
    /**
     * @var Carbon
     */
    public $dateTime;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [
                    [
                        'dateTime',
                    ],
                    'safe',
                ],
            ]
        );
    }
}
