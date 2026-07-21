<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTriage() || $user->isWard() || $user->isSpecialtyDoctor() || $user->isWardDoctor();
    }

    public function view(User $user, Patient $patient): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTriage();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isTriage() || $user->isTriageDoctor();
    }

    public function move(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isTriage() || $user->isWard();
    }

    public function discharge(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isTriage() || $user->isWard();
    }
}
