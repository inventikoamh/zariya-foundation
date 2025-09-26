<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Certificate template name
                $table->text('description')->nullable(); // Description of the certificate
                $table->string('type'); // Type: 'monetary', 'materialistic', 'service', 'general'
                $table->string('template_image'); // Path to the certificate template image
                $table->json('name_position'); // Position where donor name will be placed (x, y coordinates)
                $table->string('name_font_family')->default('Arial'); // Font family for donor name
                $table->integer('name_font_size')->default(24); // Font size for donor name
                $table->string('name_font_color')->default('#000000'); // Font color for donor name
                $table->boolean('name_bold')->default(false); // Whether donor name should be bold
                $table->boolean('name_italic')->default(false); // Whether donor name should be italic
                $table->json('date_position')->nullable(); // Position for date (optional)
                $table->string('date_font_family')->default('Arial'); // Font family for date
                $table->integer('date_font_size')->default(16); // Font size for date
                $table->string('date_font_color')->default('#666666'); // Font color for date
                $table->json('amount_position')->nullable(); // Position for amount (optional, for monetary)
                $table->string('amount_font_family')->default('Arial'); // Font family for amount
                $table->integer('amount_font_size')->default(18); // Font size for amount
                $table->string('amount_font_color')->default('#000000'); // Font color for amount
                $table->boolean('is_active')->default(true); // Whether this template is active
                $table->boolean('is_default')->default(false); // Whether this is the default template for the type
                $table->timestamps();

                // Indexes
                $table->index(['type', 'is_active']);
                $table->index('is_default');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
