<?php

namespace App\Services;

interface SmsService
{
    /**
     * Sends SMS and returns messageId
     *
     * @param $phone
     * @param $message
     *
     * @return array|boolean|null
     */
    public function sendOne($phone, $message);

    /**
     * Gets message's status
     *
     * @param $messageId
     *
     * @return string
     */
    public function getStatus($messageId);

    /**
     * Gets balance
     *
     * @return array
     */
    public function getBalance();

    /**
     * Gets message's cost
     *
     * @param $messageId
     *
     * @return float
     */
    public function getCost($messageId = null);
}
