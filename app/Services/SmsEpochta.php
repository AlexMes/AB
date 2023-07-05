<?php


namespace App\Services;

use App\SmsMessage;
use Zttp\Zttp;

class SmsEpochta implements SmsService
{
    public const QUEUE                      = '0';
    public const SEND                       = 'SENT';
    public const DELIVERED                  = 'DELIVERED';
    public const NOT_DELIVERED              = 'NOT_DELIVERED';
    public const INVALID_PHONE              = 'INVALID_PHONE_NUMBER';
    public const SPAM                       = 'SPAM';
    public const DUPLICATE                  = 'duplicate';
    public const EXPIRED                    = 'EXPIRED';

    protected $map = [
        self::QUEUE                 => SmsMessage::STATUS_QUEUE,
        self::SEND                  => SmsMessage::STATUS_SENT,
        self::DELIVERED             => SmsMessage::STATUS_OK,
        self::NOT_DELIVERED         => SmsMessage::STATUS_FAILED,
        self::INVALID_PHONE         => SmsMessage::STATUS_FAILED,
        self::SPAM                  => SmsMessage::STATUS_FAILED,
        self::DUPLICATE             => SmsMessage::STATUS_FAILED,
        self::EXPIRED               => SmsMessage::STATUS_FAILED,
    ];

    protected array $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Sends SMS and returns messageId
     *
     * @param $phone
     * @param $message
     *
     * @throws \Exception
     *
     * @return array|boolean|null
     */
    public function sendOne($phone, $message)
    {
        $params = [
            'phone'         => $phone,
            'text'          => $message,
            'key'           => $this->config['key_public'],
            'sender'        => $this->config['sender'],
            'datetime'      => '',
            'sms_lifetime'  => 0,
            'version'       => '3.0',
            'action'        => 'sendSMS',
        ];
        if ($this->config['test_mode']) {
            $params['test'] = 1;
        }
        $params['sum'] = $this->controlSum($params);

        $response = Zttp::get($this->config['url'] . "/sendSMS", $params)->json();
        $this->checkResponse($response);

        return [
            'success_request' => [
                'info' => [
                    $response['result']['id'] => $phone,
                ],
                'cost' => $response['result']['price'] ?? null,
            ],
        ];
    }

    /**
     * Gets message's status
     *
     * @param $messageId
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getStatus($messageId)
    {
        $params = [
            'id'        => $messageId,
            'key'       => $this->config['key_public'],
            'version'   => '3.0',
            'action'    => 'getCampaignDeliveryStats',
        ];
        $params['sum'] = $this->controlSum($params);

        $response = Zttp::get($this->config['url'] . "/getCampaignDeliveryStats", $params)->json();
        $this->checkResponse($response);

        return $this->map[$response['result']['status'][0]];
    }

    /**
     * Gets balance
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getBalance()
    {
        $params = [
            'key'       => $this->config['key_public'],
            'version'   => '3.0',
            'action'    => 'getUserBalance',
        ];
        $params['sum'] = $this->controlSum($params);

        $response = Zttp::get($this->config['url'] . "/getUserBalance", $params)->json();
        if (!isset($response['result']['balance_currency'])) {
            throw new \Exception('Could not get balance.');
        }

        return [
            'money'     => $response['result']['balance_currency'],
            'currency'  => $response['result']['currency'],
        ];
    }

    /**
     * Gets message's cost
     *
     * @param $messageId
     *
     * @throws \Exception
     *
     * @return float
     */
    public function getCost($messageId = null)
    {
        if (empty($messageId)) {
            return floatval($this->config['cost'] ?? 0);
        }

        $params = [
            'id'        => $messageId,
            'key'       => $this->config['key_public'],
            'version'   => '3.0',
            'action'    => 'getCampaignInfo',
        ];
        $params['sum'] = $this->controlSum($params);

        $response = Zttp::get($this->config['url'] . "/getCampaignInfo", $params)->json();
        if (!isset($response['result']['price'])) {
            throw new \Exception('Could not get cost.');
        }

        return (float)$response['result']['price'];
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function controlSum(array $params)
    {
        ksort($params);

        $sum = '';
        foreach ($params as $k=>$v) {
            $sum .= $v;
        }
        $sum .= $this->config['key_private'];

        return md5($sum);
    }

    /**
     * @param $response
     *
     * @throws \Exception
     */
    protected function checkResponse($response)
    {
        if (!isset($response['result']['id'])) {
            if (!isset($response['error'])) {
                throw new \Exception('Unknown exception.');
            }

            $errorMessage = $response['error'] . ":" . $response['code'] ?? '';
            throw new \Exception($errorMessage);
        }
    }
}
