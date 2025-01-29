<?php

namespace App\Listeners;

use App\Events\UserInvitation\UserInvitationCreated;
use App\Mail\UserInvitationEmail;
use App\Models\States\UserInvitation\Sent;
use Illuminate\Mail\SentMessage;

class SendUserInvitationEmail
{
    /**
     * Handle the event.
     *
     * @param  UserInvitationCreated  $event
     * @return void
     */
    public function handle(UserInvitationCreated $event)
    {
        $inviteEmailSent = \Mail::to($event->userInvitation->email)->send(
            new UserInvitationEmail($event->userInvitation)
        );

        if ($inviteEmailSent instanceof SentMessage) {
            $event->userInvitation->status->transitionTo(Sent::class);
        }
    }
}
