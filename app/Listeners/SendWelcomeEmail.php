<?php

namespace App\Listeners;

use App\Services\EmailNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Registered;

class SendWelcomeEmail
{
    protected $emailService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $this->emailService->sendWelcomeEmail($event->user);
    }
}
