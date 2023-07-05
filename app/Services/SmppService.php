<?php

namespace App\Services;

use App\SmsMessage;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Log;
use SMPP;
use SmppAddress;
use SmppClient;
use SmppException;
use SocketTransport;

class SmppService implements SmsService
{
    public const STATUS_ENROUTE         = '1';
    public const STATUS_DELIVERED       = '2';
    public const STATUS_EXPIRED         = '3';
    public const STATUS_DELETED         = '4';
    public const STATUS_UNDELIVERABLE   = '5';
    public const STATUS_ACCEPTED        = '6';
    public const STATUS_UNKNOWN         = '7';
    public const STATUS_REJECTED        = '8';

    /**
     * Config repository.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The SMPP Client implementation.
     *
     * @var SmppClient
     */
    protected $smpp;

    /**
     * Providers configuration.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Current provider.
     *
     * @var string
     */
    protected $provider = 'default';

    /**
     * Catchable SMPP exceptions.
     *
     * @var array
     */
    protected $catchables = [];

    protected $map = [
        self::STATUS_ENROUTE       => SmsMessage::STATUS_SENT,
        self::STATUS_DELIVERED     => SmsMessage::STATUS_OK,
        self::STATUS_ACCEPTED      => SmsMessage::STATUS_OK,
        self::STATUS_DELETED       => SmsMessage::STATUS_OK,
        self::STATUS_EXPIRED       => SmsMessage::STATUS_FAILED,
        self::STATUS_UNDELIVERABLE => SmsMessage::STATUS_FAILED,
        self::STATUS_UNKNOWN       => SmsMessage::STATUS_FAILED,
        self::STATUS_REJECTED      => SmsMessage::STATUS_FAILED,
    ];

    /**
     * SmppService constructor.
     *
     * @param Repository $config
     */
    public function __construct()
    {
        $this->config     = config();
        $this->providers  = config('smpp.providers', []);
        $this->catchables = config('smpp.transport.catchables', []);

        SmppClient::$csms_method                     = SmppClient::CSMS_8BIT_UDH;
        SmppClient::$system_type                     = config('smpp.client.system_type', 'default');
        SmppClient::$sms_null_terminate_octetstrings = config('smpp.client.null_terminate_octetstrings', false);
        SocketTransport::$forceIpv4                  = config('smpp.transport.force_ipv4', true);
        SocketTransport::$defaultDebug               = config('smpp.transport.debug', false);
    }

    /**
     * Send a single SMS.
     *
     * @param $phone
     * @param $message
     *
     * @throws SmppException
     *
     * @return array|boolean|null
     */
    public function sendOne($phone, $message)
    {
        $this->setupSmpp();

        $id = $this->sendSms($this->getSender(), $phone, $message);

        $this->smpp->close();

        return [
            "success_request" => [
                "info" => [
                    $id => $phone,
                ],
            ],
        ];
    }

    /**
     * Gets status of the message
     *
     * @param $messageId
     *
     * @return string
     */
    public function getStatus($messageId)
    {
        $this->setupSmpp();

        $response = $this->smpp->queryStatus($messageId, $this->getSender());

        $this->smpp->close();

        return $this->map[$response['message_state']];
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
        return (float)config('smpp.cost', 0);
    }

    /**
     * Alert error occured while sending SMSes.
     *
     * @param Exception $ex
     * @param int       $phone
     */
    protected function alertSendingError(Exception $ex, $phone)
    {
        Log::alert(sprintf('SMPP bulk sending error: %s. Exception: %s', $phone, $ex->getMessage()));
    }

    /**
     * Setup SMPP transport and client.
     *
     * @param mixed $bindType
     *
     * @throws SmppException
     *
     * @return SmppClient
     */
    protected function setupSmpp($bindType = 'bindTransmitter')
    {
        // Trying all available providers
        foreach ($this->providers as $provider => $config) {
            $transport = new SocketTransport([$config['host']], $config['port']);

            try {
                $transport->setRecvTimeout($config['timeout']);
                $this->smpp        = new SmppClient($transport);
                $this->smpp->debug = SocketTransport::$defaultDebug;

                $transport->open();
                if ($bindType == 'bindReceiver') {
                    $this->smpp->bindReceiver($config['login'], $config['password']);
                } else {
                    $this->smpp->bindTransmitter($config['login'], $config['password']);
                }

                $this->provider = $provider;

                break;
            }
            // Skipping unavailable
            catch (SmppException $ex) {
                $transport->close();
                $this->smpp = null;

                if (in_array($ex->getCode(), $this->catchables)) {
                    continue;
                }

                throw $ex;
            }
        }
    }

    /**
     * Return sender as SmppAddress.
     *
     * @return SmppAddress
     */
    protected function getSender()
    {
        return $this->getSmppAddress();
    }

    /**
     * Return recipient as SmppAddress.
     *
     * @param $phone
     *
     * @return SmppAddress
     */
    protected function getRecipient($phone)
    {
        return $this->getSmppAddress($phone);
    }

    /**
     * Return an SmppAddress instance based on the given phone.
     *
     * @param int|null $phone
     *
     * @return SmppAddress
     */
    protected function getSmppAddress($phone = null)
    {
        if ($phone === null) {
            $phone  = $this->getConfig('sender');
            $prefix = 'source';
        } else {
            $prefix = 'destination';
        }

        return new SmppAddress(
            $phone,
            hexdec($this->getConfig(sprintf('%s_ton', $prefix))),
            hexdec($this->getConfig(sprintf('%s_npi', $prefix)))
        );
    }

    /**
     * Send SMS via SMPP.
     *
     * @param SmppAddress $sender
     * @param int         $recipient
     * @param string      $message
     *
     * @return string
     */
    protected function sendSms(SmppAddress $sender, $recipient, $message)
    {
        $message = iconv('UTF-8', 'UCS-2BE', $message);

        return
            $this->smpp->sendSMS($sender, $this->getRecipient($recipient), $message, null, SMPP::DATA_CODING_UCS2);
    }

    /**
     * Return SMPP config item for the current provider.
     *
     * @param string $option
     *
     * @return mixed
     */
    protected function getConfig($option)
    {
        $key     = $this->provider . '.' . $option;
        $default = $this->config->get(sprintf('smpp.defaults.%s', $option));

        return Arr::get($this->providers, $key, $default);
    }
}
