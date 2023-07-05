<?php

namespace App\Http\Controllers;

use App\Facebook\FacebookApp;
use Illuminate\Http\Request;

class FacebookAppsController extends Controller
{
    /**
     * FacebookAppsController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(FacebookApp::class, 'facebook_app');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        return FacebookApp::query()
            ->orderBy('order')
            ->unless(
                $request->boolean('all'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get(),
            );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response(FacebookApp::create($request->validate([
            'id'            => ['string', 'required'],
            'name'          => ['string', 'required'],
            'secret'        => ['string', 'required'],
            'default_token' => ['string', 'nullable'],
            'domain'        => ['string', 'required'],
        ])), 201);
    }

    /**
     * @param FacebookApp $facebookApp
     * @param Request     $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(FacebookApp $facebookApp, Request $request)
    {
        FacebookApp::forget($facebookApp->id);

        return response(tap($facebookApp)->update($request->validate([
            'id'            => ['string', 'required'],
            'name'          => ['string', 'required'],
            'secret'        => ['string', 'required'],
            'default_token' => ['string', 'nullable'],
            'domain'        => ['string', 'required'],
        ])), 202);
    }
}
