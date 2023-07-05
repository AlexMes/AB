<?php

namespace App\Facebook;

use FacebookAds\Api;
use FacebookAds\Object\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Class Facebook
 *
 * @method static FacebookApp cache(string $id): FacebookApp
 * @method static FacebookApp init(string|Profile $param): FacebookApp
 * @method static \Facebook\Facebook build(string|Profile $param): \Facebook\Facebook
 * @method static Api|null initApi(string|Profile $param, string $token = null): Api|null
 * @method static FacebookApp|null find(string|FacebookApp $id): FacebookApp|null
 * @method static bool forget(string $id): bool
 * @method static bool forgetAll(): bool
 * @method static FacebookApp fromRequest(Request $request): FacebookApp
 * @method static Application initInstance(string|Profile $param, string $token = null): Application
 * @method static FacebookApp current(): FacebookApp
 *
 * @property-read string $callback_route
 *
 * @package App\Facebook\Concerns
 */
class Facebook extends Facade
{
    /**
     * Get facade service container key
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'facebook';
    }
}
