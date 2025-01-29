<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Models\User;

class UserObserver
{
    /**
     * @param User $user
     * @return void
     */
    public function updating(User $user): User
    {
        if ($user->isOwner()) {
            Tenant::where('email', $user->getOriginal('email'))
                ->update($user->only(['email']));
        }

        if ($user->isDirty('password')) {
            $user->password = bcrypt($user->password);
        }

        return $user;
    }

    /**
     * @param User $user
     * @return string
     */
    public function creating(User $user): string
    {
        $user->password = bcrypt($user->password);

        return $user;
    }
}
