<?php

namespace App\Integrations\Concerns;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Integrations\Policies\FormPolicy;
use App\Integrations\Policies\PayloadPolicy;
use Illuminate\Support\Facades\Gate;

trait AccessPolicy
{

    /**
     * List of model access bindings
     *
     * @var array
     */
    protected $policies = [
        Form::class    => FormPolicy::class,
        Payload::class => PayloadPolicy::class,
    ];

    /**
     * Register module policies
     *
     * @return void
     */
    protected function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
