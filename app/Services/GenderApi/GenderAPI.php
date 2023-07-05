<?php

namespace App\Services\GenderApi;

use Zttp\Zttp;

class GenderAPI
{
    /**
     * Map function genders to used in system
     *
     * @var array
     */
    protected array $genderMap = [
        'unknown' => 0,
        'male'    => 1,
        'female'  => 2,
    ];

    /**
     * API key
     *
     * @var string
     */
    protected string $token;

    /**
     * GenderAPI base url
     *
     * @var string
     */
    protected string $url = 'https://gender-api.com/v2/gender';

    /**
     * GenderAPI constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Detect gender using name
     *
     * @param $name
     *
     * @return int|mixed
     */
    public function detect(string $name)
    {
        $response = Zttp::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post($this->url, ['full_name' => $name]);

        if ($response->isOk()) {
            if ($response->json()['result_found'] === false) {
                return $this->genderMap['unknown'];
            }

            $gender = $response->json()['gender'];

            return $this->genderMap[$gender ?? 'unknown'];
        }

        return 0;
    }
}
