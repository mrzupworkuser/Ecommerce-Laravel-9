<?php

namespace App\Listeners;

use App\Events\UserInvitation\UserInvitationAccepted;
use Carbon\Carbon;

class AcceptInvite
{
    /**
     * Handle the event.
     *
     * @param  UserInvitationAccepted  $event
     * @return void
     */
    public function handle(UserInvitationAccepted $event)
    {
        $event->userInvitation->update([
            'accepted_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $event->userInvitation->user->assignAccess();
    }
}
