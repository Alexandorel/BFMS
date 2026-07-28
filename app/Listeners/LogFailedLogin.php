<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use OwenIt\Auditing\Models\Audit;

class LogFailedLogin
{
    /**
     * Handle the event.
     *
     * Se declanșează automat de fiecare dată când Auth::attempt() eșuează
     * (email inexistent sau parolă greșită). Util pentru detectarea
     * încercărilor de brute-force sau acces neautorizat (NFR-1).
     */
    public function handle(Failed $event): void
    {
        // $event->user poate fi null (email-ul introdus nu există deloc în DB)
        $userId   = $event->user?->id;
        $userType = $event->user ? get_class($event->user) : null;

        Audit::create([
            'user_type'      => $userType,
            'user_id'        => $userId,
            'event'          => 'failed_login',
            'auditable_type' => $userType ?? 'App\Models\User',
            'auditable_id'   => $userId ?? 0,
            'old_values'     => [],
            'new_values'     => [
                'email_incercat' => $event->credentials['email'] ?? null,
            ],
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'tags'           => 'auth,security',
        ]);
    }
}
