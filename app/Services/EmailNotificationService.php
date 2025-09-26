<?php

namespace App\Services;

use App\Models\EmailNotification;
use App\Models\EmailTemplate;
use App\Models\EmailSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function sendNotification($type, $recipientEmail, $recipientName = null, $data = [])
    {
        try {
            // Check if this notification type is enabled
            $setting = EmailSetting::where('type', $type)->first();
            if (!$setting || !$setting->isEnabled()) {
                Log::info("Email notification type '{$type}' is disabled or not found");
                return false;
            }

            // Get the email template
            $template = EmailTemplate::where('type', $type)->active()->first();
            if (!$template) {
                Log::error("No active email template found for type '{$type}'");
                return false;
            }

            // Render the email content
            $subject = $template->renderSubject($data);
            $body = $template->renderBody($data);

            // Create email notification record
            $notification = EmailNotification::create([
                'type' => $type,
                'subject' => $subject,
                'body' => $body,
                'data' => $data,
                'recipient_email' => $recipientEmail,
                'recipient_name' => $recipientName,
                'status' => 'pending',
            ]);

            // Send the email
            $this->sendEmail($notification);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email notification: " . $e->getMessage());
            return false;
        }
    }

    private function sendEmail(EmailNotification $notification)
    {
        try {
            Mail::html($notification->body, function ($message) use ($notification) {
                $message->to($notification->recipient_email, $notification->recipient_name)
                        ->subject($notification->subject);
            });

            $notification->markAsSent();
            Log::info("Email sent successfully to {$notification->recipient_email}");
        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            Log::error("Failed to send email to {$notification->recipient_email}: " . $e->getMessage());
        }
    }

    public function sendWelcomeEmail($user)
    {
        return $this->sendNotification('registration', $user->email, $user->name, [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'login_url' => route('login'),
            'foundation_name' => config('app.name', 'Foundation CRM'),
        ]);
    }

    public function sendDonationConfirmation($user, $donation)
    {
        return $this->sendNotification('donation', $user->email, $user->name, [
            'user_name' => $user->name,
            'donation_amount' => $donation->amount,
            'donation_type' => $donation->type,
            'donation_date' => $donation->created_at->format('M d, Y'),
            'foundation_name' => config('app.name', 'Foundation CRM'),
        ]);
    }

    public function sendAchievementEarned($user, $achievement)
    {
        return $this->sendNotification('achievement_earned', $user->email, $user->name, [
            'user_name' => $user->name,
            'achievement_name' => $achievement->name,
            'achievement_description' => $achievement->description,
            'achievement_points' => $achievement->points,
            'achievement_rarity' => $achievement->rarity,
            'foundation_name' => config('app.name', 'Foundation CRM'),
        ]);
    }

    public function sendProfileUpdateNotification($user)
    {
        return $this->sendNotification('profile_update', $user->email, $user->name, [
            'user_name' => $user->name,
            'update_date' => now()->format('M d, Y'),
            'foundation_name' => config('app.name', 'Foundation CRM'),
        ]);
    }

    public function sendNewAchievementNotification($user, $achievement)
    {
        return $this->sendNotification('achievement_created', $user->email, $user->name, [
            'user_name' => $user->name,
            'achievement_name' => $achievement->name,
            'achievement_description' => $achievement->description,
            'foundation_name' => config('app.name', 'Foundation CRM'),
        ]);
    }

    public function getNotificationStats()
    {
        return [
            'total' => EmailNotification::count(),
            'pending' => EmailNotification::pending()->count(),
            'sent' => EmailNotification::sent()->count(),
            'failed' => EmailNotification::failed()->count(),
        ];
    }

    public function retryFailedNotifications()
    {
        $failedNotifications = EmailNotification::failed()->get();
        $retryCount = 0;

        foreach ($failedNotifications as $notification) {
            try {
                $this->sendEmail($notification);
                $retryCount++;
            } catch (\Exception $e) {
                Log::error("Retry failed for notification {$notification->id}: " . $e->getMessage());
            }
        }

        return $retryCount;
    }
}
