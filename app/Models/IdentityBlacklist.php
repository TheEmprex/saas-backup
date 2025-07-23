<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityBlacklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone_number', 
        'id_document_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'address',
        'ip_address',
        'reason',
        'type',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if an email is blacklisted
     */
    public static function isEmailBlacklisted(string $email): bool
    {
        return self::where('email', $email)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if a phone number is blacklisted
     */
    public static function isPhoneBlacklisted(string $phone): bool
    {
        return self::where('phone_number', $phone)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if a document number is blacklisted
     */
    public static function isDocumentBlacklisted(string $documentNumber): bool
    {
        return self::where('id_document_number', $documentNumber)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if an identity combination is blacklisted
     */
    public static function isIdentityBlacklisted(string $firstName, string $lastName, string $dateOfBirth): bool
    {
        return self::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('date_of_birth', $dateOfBirth)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if an IP address is blacklisted
     */
    public static function isIpBlacklisted(string $ip): bool
    {
        return self::where('ip_address', $ip)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Add a blacklist entry for a user with all their data
     */
    public static function blacklistUser(User $user, string $reason, int $createdBy): void
    {
        $kyc = $user->kycVerification;
        
        // Blacklist email
        if ($user->email) {
            self::create([
                'email' => $user->email,
                'reason' => $reason,
                'type' => 'email',
                'created_by' => $createdBy,
            ]);
        }

        // Blacklist phone if exists
        if ($user->phone_number) {
            self::create([
                'phone_number' => $user->phone_number,
                'reason' => $reason,
                'type' => 'phone',
                'created_by' => $createdBy,
            ]);
        }

        // Blacklist KYC data if exists
        if ($kyc) {
            // Document blacklist
            self::create([
                'id_document_number' => $kyc->id_document_number,
                'reason' => $reason,
                'type' => 'document',
                'created_by' => $createdBy,
            ]);

            // Identity blacklist
            self::create([
                'first_name' => $kyc->first_name,
                'last_name' => $kyc->last_name,
                'date_of_birth' => $kyc->date_of_birth,
                'reason' => $reason,
                'type' => 'identity',
                'created_by' => $createdBy,
            ]);

            // Address blacklist
            self::create([
                'address' => $kyc->address . ', ' . $kyc->city . ', ' . $kyc->postal_code . ', ' . $kyc->country,
                'reason' => $reason,
                'type' => 'address',
                'created_by' => $createdBy,
            ]);
        }
    }
}
