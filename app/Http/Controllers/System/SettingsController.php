<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function general()
    {
        return view('system.settings.general', [
            'crm_name' => SystemSetting::get('crm_name', config('app.name')),
            'logo' => SystemSetting::get('crm_logo'),
            'favicon' => SystemSetting::get('crm_favicon'),
            'org_name' => SystemSetting::get('org_name'),
            'org_tagline' => SystemSetting::get('org_tagline'),
            'org_description' => SystemSetting::get('org_description'),
            'default_locale' => SystemSetting::get('default_locale', config('app.locale')),
            'timezone' => SystemSetting::get('timezone', config('app.timezone')),
            'currency' => SystemSetting::get('currency', 'USD'),
            'primary_color' => SystemSetting::get('primary_color', '#4f46e5'),
            'secondary_color' => SystemSetting::get('secondary_color', '#0ea5e9'),
            'contact_email' => SystemSetting::get('contact_email'),
            'contact_phone' => SystemSetting::get('contact_phone'),
            'contact_address' => SystemSetting::get('contact_address'),
            'facebook_url' => SystemSetting::get('facebook_url'),
            'twitter_url' => SystemSetting::get('twitter_url'),
            'instagram_url' => SystemSetting::get('instagram_url'),
            'linkedin_url' => SystemSetting::get('linkedin_url'),
            'maintenance_user' => SystemSetting::get('maintenance_user', '0') === '1',
            'maintenance_volunteer' => SystemSetting::get('maintenance_volunteer', '0') === '1',
            'maintenance_admin' => SystemSetting::get('maintenance_admin', '0') === '1',
        ]);
    }

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'crm_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'org_name' => 'nullable|string|max:255',
            'org_tagline' => 'nullable|string|max:255',
            'org_description' => 'nullable|string|max:2000',
            'default_locale' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:64',
            'currency' => 'nullable|string|max:10',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'maintenance_user' => 'nullable|boolean',
            'maintenance_volunteer' => 'nullable|boolean',
            'maintenance_admin' => 'nullable|boolean',
        ]);

        SystemSetting::set('crm_name', $data['crm_name']);

        if ($request->file('logo')) {
            $path = $request->file('logo')->store('system', 'public');
            SystemSetting::set('crm_logo', $path);
        }
        if ($request->file('favicon')) {
            $path = $request->file('favicon')->store('system', 'public');
            SystemSetting::set('crm_favicon', $path);
        }

        SystemSetting::set('org_name', $data['org_name'] ?? null);
        SystemSetting::set('org_tagline', $data['org_tagline'] ?? null);
        SystemSetting::set('org_description', $data['org_description'] ?? null);
        SystemSetting::set('default_locale', $data['default_locale'] ?? null);
        SystemSetting::set('timezone', $data['timezone'] ?? null);
        SystemSetting::set('currency', $data['currency'] ?? null);
        SystemSetting::set('primary_color', $data['primary_color'] ?? null);
        SystemSetting::set('secondary_color', $data['secondary_color'] ?? null);
        SystemSetting::set('contact_email', $data['contact_email'] ?? null);
        SystemSetting::set('contact_phone', $data['contact_phone'] ?? null);
        SystemSetting::set('contact_address', $data['contact_address'] ?? null);
        SystemSetting::set('facebook_url', $data['facebook_url'] ?? null);
        SystemSetting::set('twitter_url', $data['twitter_url'] ?? null);
        SystemSetting::set('instagram_url', $data['instagram_url'] ?? null);
        SystemSetting::set('linkedin_url', $data['linkedin_url'] ?? null);
        SystemSetting::set('maintenance_user', ($data['maintenance_user'] ?? false) ? '1' : '0');
        SystemSetting::set('maintenance_volunteer', ($data['maintenance_volunteer'] ?? false) ? '1' : '0');
        SystemSetting::set('maintenance_admin', ($data['maintenance_admin'] ?? false) ? '1' : '0');

        return back()->with('success', 'General settings saved.');
    }

    public function smtp()
    {
        $env = [
            'MAIL_MAILER' => env('MAIL_MAILER'),
            'MAIL_HOST' => env('MAIL_HOST'),
            'MAIL_PORT' => env('MAIL_PORT'),
            'MAIL_USERNAME' => env('MAIL_USERNAME'),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
        ];
        return view('system.settings.smtp', compact('env'));
    }

    public function saveSmtp(Request $request)
    {
        $data = $request->validate([
            'MAIL_MAILER' => 'required|string',
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|numeric',
            'MAIL_USERNAME' => 'nullable|string',
            'MAIL_PASSWORD' => 'nullable|string',
            'MAIL_ENCRYPTION' => 'nullable|string',
            'MAIL_FROM_ADDRESS' => 'nullable|email',
            'MAIL_FROM_NAME' => 'nullable|string',
        ]);

        $this->writeEnv($data);

        return back()->with('success', 'SMTP settings saved.');
    }

    public function testSmtp(Request $request)
    {
        $data = $request->validate([
            'to' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test email from Foundation CRM SMTP settings.', function ($message) use ($data) {
                $message->to($data['to'])
                    ->subject('SMTP Test Email');
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send test email: '.$e->getMessage());
        }

        return back()->with('success', 'Test email sent successfully to '.$data['to']);
    }

    public function frontpage()
    {
        return view('system.settings.frontpage', [
            'title' => SystemSetting::get('front_title', 'Zariya Foundation - Making a Difference Together'),
            'headline' => SystemSetting::get('front_headline', 'Zariya Foundation'),
            'subheadline' => SystemSetting::get('front_subheadline', 'Making a difference together. Join us in creating positive change through donations, volunteer work, and community support.'),
            'cta_primary_text' => SystemSetting::get('front_cta_primary_text', 'Donate Now'),
            'cta_primary_link' => SystemSetting::get('front_cta_primary_link', route('wizard', 'donation')),
            'cta_secondary_text' => SystemSetting::get('front_cta_secondary_text', 'Request Assistance'),
            'cta_secondary_link' => SystemSetting::get('front_cta_secondary_link', route('wizard', 'beneficiary')),
            'features' => SystemSetting::get('front_features', [
                ['title' => 'Monetary Donations', 'text' => 'Support our cause with financial contributions that help us reach more people in need.'],
                ['title' => 'Material Donations', 'text' => 'Donate physical items like clothes, food, books, and other essentials to help those in need.'],
                ['title' => 'Service Donations', 'text' => 'Offer your time and skills to volunteer and make a direct impact in your community.'],
            ]),
            'background_from' => SystemSetting::get('front_bg_from', '#eff6ff'),
            'background_to' => SystemSetting::get('front_bg_to', '#e0e7ff'),
        ]);
    }

    public function saveFrontpage(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'headline' => 'required|string|max:255',
            'subheadline' => 'required|string|max:1000',
            'cta_primary_text' => 'required|string|max:100',
            'cta_primary_link' => 'required|string|max:255',
            'cta_secondary_text' => 'required|string|max:100',
            'cta_secondary_link' => 'required|string|max:255',
            'background_from' => 'nullable|string|max:20',
            'background_to' => 'nullable|string|max:20',
            'features' => 'nullable|array|max:6',
            'features.*.title' => 'nullable|string|max:100',
            'features.*.text' => 'nullable|string|max:300',
        ]);

        SystemSetting::set('front_title', $data['title']);
        SystemSetting::set('front_headline', $data['headline']);
        SystemSetting::set('front_subheadline', $data['subheadline']);
        SystemSetting::set('front_cta_primary_text', $data['cta_primary_text']);
        SystemSetting::set('front_cta_primary_link', $data['cta_primary_link']);
        SystemSetting::set('front_cta_secondary_text', $data['cta_secondary_text']);
        SystemSetting::set('front_cta_secondary_link', $data['cta_secondary_link']);
        SystemSetting::set('front_bg_from', $data['background_from'] ?? null);
        SystemSetting::set('front_bg_to', $data['background_to'] ?? null);
        if (isset($data['features'])) {
            $cleanFeatures = [];
            foreach ($data['features'] as $feature) {
                $title = isset($feature['title']) ? trim((string) $feature['title']) : '';
                $text = isset($feature['text']) ? trim((string) $feature['text']) : '';
                if ($title === '' && $text === '') {
                    continue;
                }
                $cleanFeatures[] = [
                    'title' => $title,
                    'text' => $text,
                ];
                if (count($cleanFeatures) >= 6) {
                    break;
                }
            }
            if (!empty($cleanFeatures)) {
                SystemSetting::set('front_features', $cleanFeatures, 'json');
            } else {
                // If all were empty, clear saved features
                SystemSetting::set('front_features', [], 'json');
            }
        }

        return back()->with('success', 'Front page content saved.');
    }

    public function cron()
    {
        // Get scheduled tasks information
        $scheduledTasks = [];

        // Check if scheduler is running
        $schedulerRunning = false;
        try {
            $output = shell_exec('ps aux | grep "schedule:work" | grep -v grep');
            $schedulerRunning = !empty(trim($output));
        } catch (\Exception $e) {
            // Ignore errors
        }

        // Get last run time
        $lastRun = null;
        try {
            $lastRun = \Cache::get('laravel_schedule_last_run');
        } catch (\Exception $e) {
            // Ignore errors
        }

        return view('system.settings.cron', compact('scheduledTasks', 'schedulerRunning', 'lastRun'));
    }

    private function writeEnv(array $pairs): void
    {
        $path = base_path('.env');
        $content = file_get_contents($path);
        foreach ($pairs as $key => $value) {
            $value = str_replace(['"', "\n"], ['\\"', ''], (string) $value);
            if (preg_match("/^{$key}=.*$/m", $content)) {
                $content = preg_replace("/^{$key}=.*$/m", $key.'="'.$value.'"', $content);
            } else {
                $content .= PHP_EOL.$key.'="'.$value.'"';
            }
        }
        file_put_contents($path, $content);
    }
}



