<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Events\RefreshTokenCreated;

class PruneOldTokens
{
    /**
     * Handle the event.
     *
     * @param \Laravel\Passport\Events\RefreshTokenCreated $event
     * @return void
     * @throws \Throwable
     */
    public function handle(RefreshTokenCreated $event)
    {
        DB::transaction(function () use ($event) {
            DB::table('oauth_refresh_tokens')
              ->where('id', '<>', $event->refreshTokenId)
              ->where('access_token_id', '<>', $event->accessTokenId)
              ->update(['revoked' => true]);
        });
    }
}
