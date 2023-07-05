<?php

namespace App\Services;

use App\SmsMessage;
use Zttp\Zttp;

class SmsClubService implements SmsService
{
    protected $map = [
        'ENROUTE' => SmsMessage::STATUS_SENT,
        'DELIVRD' => SmsMessage::STATUS_OK,
        'EXPIRED' => SmsMessage::STATUS_FAILED,
        'UNDELIV' => SmsMessage::STATUS_FAILED,
        'REJECTD' => SmsMessage::STATUS_FAILED,
    ];

    /**
     * Sends SMS and returns messageId
     *
     * @param $phone
     * @param $message
     *
     * @return array|boolean|null
     */
    public function sendOne($phone, $message)
    {
        return Zttp::withHeaders([
            'Authorization'  => 'Bearer ' . config('services.smsclub.token'),
        ])->post('https://im.smsclub.mobi/sms/send', [
            'src_addr'  => config('services.smsclub.alfa_name'),
            'phone'     => [$phone],
            'message'   => $message,
        ])->json();
    }

    /**
     * Gets message's status
     *
     * @param $messageId
     *
     * @return string
     */
    public function getStatus($messageId)
    {
        $response = Zttp::withHeaders([
            'Authorization'  => 'Bearer ' . config('services.smsclub.token'),
        ])->post('https://im.smsclub.mobi/sms/status', [
            'id_sms' => [$messageId],
        ])->json();

        return $this->map[$response['success_request']['info'][$messageId]];
    }

    /**
     * Gets balance
     *
     * @return array
     */
    public function getBalance()
    {
        $response = Zttp::withHeaders([
            'Authorization'  => 'Bearer ' . config('services.smsclub.token'),
        ])->post('https://im.smsclub.mobi/sms/balance')->json();

        // contains 'money' and 'currency' fields
        return $response['success_request']['info'];
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
        return (float)config('services.smsclub.cost', 0);
    }
}
