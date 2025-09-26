<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default certificate templates for each type

        // Monetary Donation Certificate
        Certificate::create([
            'name' => 'Monetary Donation Certificate',
            'description' => 'Default certificate template for monetary donations',
            'type' => 'monetary',
            'template_image' => 'certificates/templates/default-monetary.png',
            'name_position' => ['x' => 400, 'y' => 300],
            'name_font_family' => 'Arial',
            'name_font_size' => 28,
            'name_font_color' => '#1f2937',
            'name_bold' => true,
            'name_italic' => false,
            'date_position' => ['x' => 400, 'y' => 350],
            'date_font_family' => 'Arial',
            'date_font_size' => 16,
            'date_font_color' => '#6b7280',
            'amount_position' => ['x' => 400, 'y' => 380],
            'amount_font_family' => 'Arial',
            'amount_font_size' => 20,
            'amount_font_color' => '#059669',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Materialistic Donation Certificate
        Certificate::create([
            'name' => 'Materialistic Donation Certificate',
            'description' => 'Default certificate template for materialistic donations',
            'type' => 'materialistic',
            'template_image' => 'certificates/templates/default-materialistic.png',
            'name_position' => ['x' => 400, 'y' => 300],
            'name_font_family' => 'Arial',
            'name_font_size' => 28,
            'name_font_color' => '#1f2937',
            'name_bold' => true,
            'name_italic' => false,
            'date_position' => ['x' => 400, 'y' => 350],
            'date_font_family' => 'Arial',
            'date_font_size' => 16,
            'date_font_color' => '#6b7280',
            'amount_position' => null,
            'amount_font_family' => 'Arial',
            'amount_font_size' => 18,
            'amount_font_color' => '#000000',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Service Donation Certificate
        Certificate::create([
            'name' => 'Service Donation Certificate',
            'description' => 'Default certificate template for service donations',
            'type' => 'service',
            'template_image' => 'certificates/templates/default-service.png',
            'name_position' => ['x' => 400, 'y' => 300],
            'name_font_family' => 'Arial',
            'name_font_size' => 28,
            'name_font_color' => '#1f2937',
            'name_bold' => true,
            'name_italic' => false,
            'date_position' => ['x' => 400, 'y' => 350],
            'date_font_family' => 'Arial',
            'date_font_size' => 16,
            'date_font_color' => '#6b7280',
            'amount_position' => null,
            'amount_font_family' => 'Arial',
            'amount_font_size' => 18,
            'amount_font_color' => '#000000',
            'is_active' => true,
            'is_default' => true,
        ]);

        // General Certificate
        Certificate::create([
            'name' => 'General Donation Certificate',
            'description' => 'Default certificate template for general donations',
            'type' => 'general',
            'template_image' => 'certificates/templates/default-general.png',
            'name_position' => ['x' => 400, 'y' => 300],
            'name_font_family' => 'Arial',
            'name_font_size' => 28,
            'name_font_color' => '#1f2937',
            'name_bold' => true,
            'name_italic' => false,
            'date_position' => ['x' => 400, 'y' => 350],
            'date_font_family' => 'Arial',
            'date_font_size' => 16,
            'date_font_color' => '#6b7280',
            'amount_position' => null,
            'amount_font_family' => 'Arial',
            'amount_font_size' => 18,
            'amount_font_color' => '#000000',
            'is_active' => true,
            'is_default' => true,
        ]);
    }
}
