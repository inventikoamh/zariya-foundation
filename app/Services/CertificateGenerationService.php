<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Donation;
use App\Models\DonorCertificate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CertificateGenerationService
{
    public function generateCertificateForDonation(Donation $donation)
    {
        // Get the appropriate certificate template
        $certificateTemplate = Certificate::getDefaultForType($donation->type);

        if (!$certificateTemplate) {
            throw new \Exception("No certificate template found for donation type: {$donation->type}");
        }

        // Generate the certificate
        $generatedImagePath = $this->generateCertificateImage($donation, $certificateTemplate);

        // Create the donor certificate record
        $donorCertificate = DonorCertificate::create([
            'donation_id' => $donation->id,
            'certificate_id' => $certificateTemplate->id,
            'donor_id' => $donation->donor_id,
            'generated_image' => $generatedImagePath,
            'donor_name' => $donation->donor->first_name . ' ' . $donation->donor->last_name,
            'amount' => $donation->type === 'monetary' ? $donation->details['amount'] ?? null : null,
            'currency' => $donation->type === 'monetary' ? $donation->details['currency'] ?? null : null,
            'donation_date' => $donation->created_at->toDateString(),
            'certificate_number' => DonorCertificate::generateCertificateNumber(),
        ]);

        return $donorCertificate;
    }

    private function generateCertificateImage(Donation $donation, Certificate $template)
    {
        // Load the template image
        $templatePath = storage_path('app/public/' . $template->template_image);

        if (!file_exists($templatePath)) {
            throw new \Exception("Certificate template image not found: {$template->template_image}");
        }

        // Create image from template
        $image = Image::make($templatePath);

        // Get donor name
        $donorName = $donation->donor->first_name . ' ' . $donation->donor->last_name;

        // Add donor name to the image
        $this->addTextToImage($image, $donorName, $template->name_position, [
            'font_family' => $template->name_font_family,
            'font_size' => $template->name_font_size,
            'color' => $template->name_font_color,
            'bold' => $template->name_bold,
            'italic' => $template->name_italic,
        ]);

        // Add date if position is specified
        if ($template->date_position) {
            $this->addTextToImage($image, $donation->created_at->format('F j, Y'), $template->date_position, [
                'font_family' => $template->date_font_family,
                'font_size' => $template->date_font_size,
                'color' => $template->date_font_color,
                'bold' => false,
                'italic' => false,
            ]);
        }

        // Add amount for monetary donations if position is specified
        if ($donation->type === 'monetary' && $template->amount_position && isset($donation->details['amount'])) {
            $amount = number_format($donation->details['amount'], 2) . ' ' . ($donation->details['currency'] ?? 'USD');
            $this->addTextToImage($image, $amount, $template->amount_position, [
                'font_family' => $template->amount_font_family,
                'font_size' => $template->amount_font_size,
                'color' => $template->amount_font_color,
                'bold' => false,
                'italic' => false,
            ]);
        }

        // Generate unique filename
        $filename = 'certificates/generated/' . uniqid() . '_' . $donation->id . '.png';

        // Save the generated image
        $image->save(storage_path('app/public/' . $filename));

        return $filename;
    }

    private function addTextToImage($image, $text, $position, $options)
    {
        $x = $position['x'] ?? 0;
        $y = $position['y'] ?? 0;

        // Convert hex color to RGB
        $color = $this->hexToRgb($options['color']);

        // Add text to image
        $image->text($text, $x, $y, function ($font) use ($options, $color) {
            $font->file(public_path('fonts/' . $options['font_family'] . '.ttf'));
            $font->size($options['font_size']);
            $font->color($color);

            if ($options['bold']) {
                $font->align('center');
            }
        });
    }

    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
}
