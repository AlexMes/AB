<?php

use App\Broadcasting;
use App\Broadcasting\LeadOrderChannel;
use App\CRM\Broadcasting\ManagerChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.User.{id}', Broadcasting\UserChannel::class);
Broadcast::channel('profiles', Broadcasting\AllowEveryone::class);
Broadcast::channel('App.Profile.{profile}', Broadcasting\Profiles::class);
Broadcast::channel('App.Account.{account}', Broadcasting\Accounts::class);
Broadcast::channel('App.Orders', Broadcasting\AllowEveryone::class);
Broadcast::channel('App.Orders.{order}', Broadcasting\AllowEveryone::class);
Broadcast::channel('App.Domains.{domain}', Broadcasting\AllowEveryone::class);
Broadcast::channel('App.Adset.{account}', Broadcasting\Adsets::class);
Broadcast::channel('App.LeadOrder.{order}', LeadOrderChannel::class);
Broadcast::channel('CRM.Manager.{manager}', ManagerChannel::class, ['guards' => ['web', 'crm']]);
Broadcast::channel('App.GoogleApp.{app}', Broadcasting\GoogleApp::class);
