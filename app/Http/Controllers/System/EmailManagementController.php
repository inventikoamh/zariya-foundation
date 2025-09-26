<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\EmailSetting;
use App\Models\EmailNotification;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailManagementController extends Controller
{
    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index()
    {
        $templates = EmailTemplate::all();
        $settings = EmailSetting::all();
        $stats = $this->emailService->getNotificationStats();

        return view('system.email.index', compact('templates', 'settings', 'stats'));
    }

    public function templates()
    {
        $templates = EmailTemplate::all();
        return view('system.email.templates', compact('templates'));
    }

    public function editTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('system.email.edit-template', compact('template'));
    }

    public function updateTemplate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template = EmailTemplate::findOrFail($id);
        $template->update($request->only(['name', 'subject', 'body_html', 'is_active']));

        return redirect()->route('system.email.templates')
            ->with('success', 'Email template updated successfully!');
    }

    public function settings()
    {
        $settings = EmailSetting::all();
        return view('system.email.settings', compact('settings'));
    }

    public function updateSetting(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'boolean',
            'settings' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $setting = EmailSetting::findOrFail($id);
        $setting->update($request->only(['enabled', 'settings']));

        return redirect()->route('system.email.settings')
            ->with('success', 'Email setting updated successfully!');
    }

    public function notifications()
    {
        $notifications = EmailNotification::orderBy('created_at', 'desc')->paginate(20);
        return view('system.email.notifications', compact('notifications'));
    }

    public function retryFailed()
    {
        $retryCount = $this->emailService->retryFailedNotifications();

        return redirect()->route('system.email.notifications')
            ->with('success', "Retried {$retryCount} failed notifications.");
    }

    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed'], 400);
        }

        $testData = [
            'user_name' => $request->name,
            'user_email' => $request->email,
            'foundation_name' => config('app.name', 'Foundation CRM'),
            'donation_amount' => '100.00',
            'donation_type' => 'monetary',
            'donation_date' => now()->format('M d, Y'),
            'achievement_name' => 'Test Achievement',
            'achievement_description' => 'This is a test achievement for email testing.',
            'achievement_points' => '50',
            'achievement_rarity' => 'rare',
            'update_date' => now()->format('M d, Y'),
            'login_url' => route('login'),
        ];

        $result = $this->emailService->sendNotification(
            $request->type,
            $request->email,
            $request->name,
            $testData
        );

        if ($result) {
            return response()->json(['success' => 'Test email sent successfully!']);
        } else {
            return response()->json(['error' => 'Failed to send test email'], 500);
        }
    }
}
