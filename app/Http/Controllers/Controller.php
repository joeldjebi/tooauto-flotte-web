<?php

namespace App\Http\Controllers;

use App\Models\GestionnaireDeFlotte;

abstract class Controller
{
    protected function currentFleetOwner(): ?GestionnaireDeFlotte
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return GestionnaireDeFlotte::find($user->fleetOwnerId());
    }

    protected function currentFleetOwnerId(): ?int
    {
        return auth()->user()?->fleetOwnerId();
    }
}
