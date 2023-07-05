<?php

namespace App\VK\Http\Controllers;

use App\Http\Controllers\Controller;
use App\VK\Exceptions\VKException;
use App\VK\Jobs\ConnectVKProfile;
use App\VK\VKApp;
use Illuminate\Http\Request;

class ProcessCallback extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param VKApp                    $vkApp
     *
     *@throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     *
     */
    public function __invoke(Request $request, VKApp $vkApp)
    {
        try {
            $token = $vkApp->getLoginHelper()
                ->getAccessTokenByCode($request->input('code'), $request->input('state'));

            ConnectVKProfile::dispatchNow($token);

            return "Your VK profile was attached. You can close this page now";
        } catch (VKException $e) {
            \Log::error('Received failure from vk.' . PHP_EOL . $e->getMessage());

            return response("Error occurred during profile attachment. This incident reported to developers. Error: " . $e->getMessage());
        }
    }
}
