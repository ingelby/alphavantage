<?php


namespace Ingelby\Alphavantage\Models;


use yii\base\Model;

class SearchMatch extends Model
{
    /**
     * @var string
     */
    public $symbol;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $region;
    /**
     * @var string
     */
    public $marketOpen;
    /**
     * @var string
     */
    public $marketClose;
    /**
     * @var string
     */
    public $timezone;
    /**
     * @var string
     */
    public $currency;
    /**
     * @var string
     */
    public $matchScore;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'symbol',
                    'name',
                    'type',
                    'region',
                    'marketOpen',
                    'marketClose',
                    'timezone',
                    'currency',
                    'matchScore',
                ],
                'safe',
            ],
        ];
    }
}
