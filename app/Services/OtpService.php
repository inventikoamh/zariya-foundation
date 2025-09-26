<?php

namespace App\Services;

class OtpService
{
    /**
     * Verify OTP for development
     * In production, this would integrate with SMS providers
     */
    public function verify(string $phone, string $otp): bool
    {
        // For development, accept fixed OTP
        return $otp === '525252';
    }

    /**
     * Generate OTP (for future SMS integration)
     */
    public function generate(): string
    {
        // For development, return fixed OTP
        return '525252';
    }

    /**
     * Send OTP via SMS (placeholder for future implementation)
     */
    public function send(string $phone, string $otp): bool
    {
        // TODO: Integrate with SMS provider
        // For now, just log the OTP
        \Log::info("OTP for {$phone}: {$otp}");
        return true;
    }
}
