<?php


namespace App\Services;

use App\SmsMessage;
use Zttp\Zttp;

class SmsProsto implements SmsService
{
    public const SEND                       = '0';
    public const DELIVERED                  = '1';
    public const NOT_DELIVERED              = '2';
    public const NOT_DELIVERED_SMSC         = '16';
    public const NOT_DELIVERED_EXPIRED      = '32';

    protected $map = [
        self::SEND                  => SmsMessage::STATUS_SENT,
        self::DELIVERED             => SmsMessage::STATUS_OK,
        self::NOT_DELIVERED         => SmsMessage::STATUS_FAILED,
        self::NOT_DELIVERED_SMSC    => SmsMessage::STATUS_FAILED,
        self::NOT_DELIVERED_EXPIRED => SmsMessage::STATUS_FAILED,
    ];

    protected array $config = [];

    public function __construct()
    {
        $this->config = config('services.smsprosto');
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
        $response = Zttp::get($this->config['url'], [
            'phone'         => (int)$phone,
            'text'          => $message,
            'format'        => 'json',
            'email'         => $this->config['login'],
            'password'      => $this->config['password'],
            'method'        => $this->config['method'],
            'sender_name'   => $this->config['sender'],
        ])->json();

        $this->checkResponse($response);

        return [
            'success_request' => [
                'info' => [
                    $response['response']['data']['id'] => $phone,
                ],
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
        $response = Zttp::get($this->config['url'], [
            'id'        => $messageId,
            'format'    => 'json',
            'email'     => $this->config['login'],
            'password'  => $this->config['password'],
            'method'    => 'get_msg_report',
        ])->json();

        $this->checkResponse($response);

        return $this->map[$response['response']['data']['state']];
    }

    /**
     * Gets balance
     *
     * @return array
     */
    public function getBalance()
    {
        return [];
    }

    /**
     * Gets message's cost
     *
     * @param $messageId
     *
     * @return float
     */
    public function getCost($messageId = null)
    {
        return floatval($this->config['cost'] ?? 0);
    }

    /**
     * @param $response
     *
     * @throws \Exception
     */
    protected function checkResponse($response)
    {
        if (!isset($response['response']['data']['id'])) {
            if (!isset($response['response']['msg']['text'])) {
                throw new \Exception('Unknown exception.');
            }

            $errorMessage = $response['response']['msg']['text'] . ":" . $response['response']['msg']['err_code'] ?? '';
            throw new \Exception($errorMessage);
        }
    }
}
