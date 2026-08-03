<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use OwenIt\Auditing\Models\Audit;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        Audit::create([
            'user_type'      => get_class($event->user),
            'user_id'        => $event->user->id,
            'event'          => 'login',
            'auditable_type' => get_class($event->user),
            'auditable_id'   => $event->user->id,
            'old_values'     => [],
            'new_values'     => [],
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'tags'           => 'auth',
        ]);
    }
}
