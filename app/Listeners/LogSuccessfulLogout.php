<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use OwenIt\Auditing\Models\Audit;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // La logout, $event->user poate fi null dacă sesiunea a expirat deja
        if (! $event->user) {
            return;
        }

        Audit::create([
            'user_type'      => get_class($event->user),
            'user_id'        => $event->user->id,
            'event'          => 'logout',
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
