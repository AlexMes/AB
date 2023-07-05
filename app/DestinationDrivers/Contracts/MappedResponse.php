<?php


namespace App\DestinationDrivers\Contracts;

interface MappedResponse
{
    /**
     * @param array|null $response
     *
     * @return mixed
     */
    public function mapResponse(?array $response = null);
}
