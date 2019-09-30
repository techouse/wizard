<?php

namespace App\Policies;

trait AdministratorCanDoEverything
{
    public function before($user, $ability)
    {
        if ($user->isAdministrator()) {
            return true;
        }
    }
}
