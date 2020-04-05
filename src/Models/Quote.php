<?php


namespace Ingelby\Alphavantage\Models;


use yii\base\Model;

class Quote extends Model
{
    /**
     * @var string
     */
    public $symbol;
    /**
     * @var string
     */
    public $open;
    /**
     * @var string
     */
    public $high;
    /**
     * @var string
     */
    public $low;
    /**
     * @var string
     */
    public $price;
    /**
     * @var string
     */
    public $volume;
    /**
     * @var string
     */
    public $latestTradingDay;
    /**
     * @var string
     */
    public $previousClose;
    /**
     * @var string
     */
    public $change;
    /**
     * @var string
     */
    public $changePercent;


    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'symbol',
                    'open',
                    'high',
                    'low',
                    'price',
                    'volume',
                    'latestTradingDay',
                    'previousClose',
                    'change',
                    'changePercent',
                ],
                'safe',
            ],
        ];
    }

    /**
     * @return bool
     */
    public function isNoChange(): bool
    {
        return 0 === (int) $this->change;
    }

    /**
     * @return bool
     */
    public function isPositive(): bool
    {
        if (strpos($this->change, '-') === false) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNegative(): bool
    {
        return !$this->isPositive();
    }
}
