<?php

namespace App\Core;

interface RoleInterface
{
    // A vérifier, donc ne pas utiliser.
    const DEFINITION = [
        "Administrateur" => "ROLE_ADMIN",
        "Moniteur" => "ROLE_MONITEUR",
        "Elève" => "ROLE_USER"
    ];
}