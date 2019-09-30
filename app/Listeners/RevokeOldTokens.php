<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Events\AccessTokenCreated;

class RevokeOldTokens
{
    /**
     * Handle the event.
     *
     * @param \Laravel\Passport\Events\AccessTokenCreated $event
     * @return void
     * @throws \Throwable
     */
    public function handle(AccessTokenCreated $event): void
    {
        DB::transaction(function () use ($event) {
            DB::table('oauth_access_tokens')
              ->where('id', '<>', $event->tokenId)
              ->where('user_id', $event->userId)
              ->where('client_id', $event->clientId)
              ->update(['revoked' => true]);
        });
    }
}
