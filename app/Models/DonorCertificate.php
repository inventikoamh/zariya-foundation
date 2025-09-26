<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'certificate_id',
        'donor_id',
        'generated_image',
        'donor_name',
        'amount',
        'currency',
        'donation_date',
        'certificate_number',
        'is_downloaded',
        'downloaded_at',
    ];

    protected $casts = [
        'donation_date' => 'date',
        'downloaded_at' => 'datetime',
        'is_downloaded' => 'boolean',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    // Scopes
    public function scopeForDonor($query, $donorId)
    {
        return $query->where('donor_id', $donorId);
    }

    public function scopeDownloaded($query)
    {
        return $query->where('is_downloaded', true);
    }

    public function scopeNotDownloaded($query)
    {
        return $query->where('is_downloaded', false);
    }

    // Accessors
    public function getGeneratedImageUrlAttribute()
    {
        return asset('storage/' . $this->generated_image);
    }

    public function getFormattedAmountAttribute()
    {
        if ($this->amount && $this->currency) {
            return number_format($this->amount, 2) . ' ' . $this->currency;
        }
        return null;
    }

    // Methods
    public function markAsDownloaded()
    {
        $this->update([
            'is_downloaded' => true,
            'downloaded_at' => now(),
        ]);
    }

    public static function generateCertificateNumber()
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('certificate_number', $number)->exists());

        return $number;
    }
}
