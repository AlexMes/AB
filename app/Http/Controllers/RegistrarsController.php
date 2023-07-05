<?php

namespace App\Http\Controllers;

use App\Registrar;
use Illuminate\Http\JsonResponse;

class RegistrarsController extends Controller
{
    /**
     * Get all registrars
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function index()
    {
        return cache()->remember('ab-registrars', now()->addHour(), function () {
            return Registrar::all();
        });
    }
}
