<?php

namespace App\Listeners;

use Laravel\Passport\Events\AccessTokenCreated;

class AddAudienceToToken
{
    public function handle(AccessTokenCreated $event) {

    }
}
