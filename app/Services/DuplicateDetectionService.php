<?php

namespace App\Services;

use App\Models\User;
use App\Models\KycVerification;
use App\Models\IdentityBlacklist;
use Illuminate\Support\Collection;

class DuplicateDetectionService
{
    /**
     * Detect potential duplicate accounts based on KYC data
     */
    public function detectDuplicatesByKycData(array $kycData): Collection
    {
        $duplicates = collect();
        
        // 1. Check for exact document number match
        $documentDuplicates = $this->findByDocumentNumber($kycData['id_document_number']);
        if ($documentDuplicates->isNotEmpty()) {
            $duplicates = $duplicates->merge($documentDuplicates);
        }
        
        // 2. Check for name + date of birth match
        $identityDuplicates = $this->findByIdentity(
            $kycData['first_name'],
            $kycData['last_name'],
            $kycData['date_of_birth']
        );
        if ($identityDuplicates->isNotEmpty()) {
            $duplicates = $duplicates->merge($identityDuplicates);
        }
        
        // 3. Check for phone number duplicates
        if (isset($kycData['phone_number'])) {
            $phoneDuplicates = $this->findByPhoneNumber($kycData['phone_number']);
            if ($phoneDuplicates->isNotEmpty()) {
                $duplicates = $duplicates->merge($phoneDuplicates);
            }
        }
        
        // 4. Check for address duplicates (strong indicator)
        $addressDuplicates = $this->findByAddress(
            $kycData['address'],
            $kycData['city'],
            $kycData['postal_code'],
            $kycData['country']
        );
        if ($addressDuplicates->isNotEmpty()) {
            $duplicates = $duplicates->merge($addressDuplicates);
        }
        
        return $duplicates->unique('user_id');
    }
    
    /**
     * Find KYC verifications by document number
     */
    private function findByDocumentNumber(string $documentNumber): Collection
    {
        return KycVerification::with('user')
            ->where('id_document_number', $documentNumber)
            ->get();
    }
    
    /**
     * Find KYC verifications by identity (name + DOB)
     */
    private function findByIdentity(string $firstName, string $lastName, string $dateOfBirth): Collection
    {
        return KycVerification::with('user')
            ->where('first_name', 'LIKE', $firstName)
            ->where('last_name', 'LIKE', $lastName)
            ->where('date_of_birth', $dateOfBirth)
            ->get();
    }
    
    /**
     * Find users by phone number
     */
    private function findByPhoneNumber(string $phoneNumber): Collection
    {
        $users = User::where('phone_number', $phoneNumber)->get();
        
        return $users->map(function ($user) {
            // Create a pseudo KYC object for consistency
            return (object) [
                'user_id' => $user->id,
                'user' => $user,
                'match_type' => 'phone'
            ];
        });
    }
    
    /**
     * Find KYC verifications by address
     */
    private function findByAddress(string $address, string $city, string $postalCode, string $country): Collection
    {
        return KycVerification::with('user')
            ->where('address', $address)
            ->where('city', $city)
            ->where('postal_code', $postalCode)
            ->where('country', $country)
            ->get();
    }
    
    /**
     * Check if KYC data matches blacklisted entries
     */
    public function checkBlacklist(array $kycData): array
    {
        $blacklistMatches = [];
        
        // Check document number
        if (IdentityBlacklist::isDocumentBlacklisted($kycData['id_document_number'])) {
            $blacklistMatches[] = [
                'type' => 'document',
                'value' => $kycData['id_document_number'],
                'message' => 'This document number is blacklisted'
            ];
        }
        
        // Check identity
        if (IdentityBlacklist::isIdentityBlacklisted(
            $kycData['first_name'],
            $kycData['last_name'],
            $kycData['date_of_birth']
        )) {
            $blacklistMatches[] = [
                'type' => 'identity',
                'value' => $kycData['first_name'] . ' ' . $kycData['last_name'],
                'message' => 'This identity is blacklisted'
            ];
        }
        
        // Check phone number
        if (isset($kycData['phone_number']) && IdentityBlacklist::isPhoneBlacklisted($kycData['phone_number'])) {
            $blacklistMatches[] = [
                'type' => 'phone',
                'value' => $kycData['phone_number'],
                'message' => 'This phone number is blacklisted'
            ];
        }
        
        return $blacklistMatches;
    }
    
    /**
     * Calculate duplicate score based on matching criteria
     */
    public function calculateDuplicateScore(Collection $duplicates): array
    {
        $scores = [];
        
        foreach ($duplicates as $duplicate) {
            $score = 0;
            $reasons = [];
            
            // Document match = 100% (definitive)
            if ($duplicate->id_document_number) {
                $score = 100;
                $reasons[] = 'Exact document number match';
            } else {
                // Identity match = 80%
                if (isset($duplicate->first_name) && isset($duplicate->last_name)) {
                    $score += 80;
                    $reasons[] = 'Name and date of birth match';
                }
                
                // Address match = 60%
                if (isset($duplicate->address)) {
                    $score += 60;
                    $reasons[] = 'Address match';
                }
                
                // Phone match = 70%
                if (isset($duplicate->match_type) && $duplicate->match_type === 'phone') {
                    $score += 70;
                    $reasons[] = 'Phone number match';
                }
            }
            
            $scores[] = [
                'duplicate' => $duplicate,
                'score' => min($score, 100), // Cap at 100%
                'reasons' => $reasons,
                'risk_level' => $this->getRiskLevel($score)
            ];
        }
        
        return $scores;
    }
    
    /**
     * Get risk level based on score
     */
    private function getRiskLevel(int $score): string
    {
        if ($score >= 100) return 'CRITICAL';
        if ($score >= 80) return 'HIGH';
        if ($score >= 60) return 'MEDIUM';
        return 'LOW';
    }
    
    /**
     * Generate duplicate report for admin review
     */
    public function generateDuplicateReport(User $user, array $kycData): array
    {
        $duplicates = $this->detectDuplicatesByKycData($kycData);
        $blacklistMatches = $this->checkBlacklist($kycData);
        $scores = $this->calculateDuplicateScore($duplicates);
        
        return [
            'user' => $user,
            'kyc_data' => $kycData,
            'duplicates_found' => $duplicates->count(),
            'duplicate_details' => $scores,
            'blacklist_matches' => $blacklistMatches,
            'recommendation' => $this->getRecommendation($scores, $blacklistMatches),
            'timestamp' => now(),
        ];
    }
    
    /**
     * Get recommendation based on duplicate analysis
     */
    private function getRecommendation(array $scores, array $blacklistMatches): string
    {
        // If any blacklist matches, immediate rejection
        if (!empty($blacklistMatches)) {
            return 'REJECT - Blacklisted data detected';
        }
        
        // If any critical duplicates, reject
        foreach ($scores as $score) {
            if ($score['risk_level'] === 'CRITICAL') {
                return 'REJECT - Critical duplicate detected';
            }
        }
        
        // If high risk duplicates, manual review
        foreach ($scores as $score) {
            if ($score['risk_level'] === 'HIGH') {
                return 'REQUIRES_REVIEW - High risk duplicate detected';
            }
        }
        
        // If medium risk duplicates, flag for review
        foreach ($scores as $score) {
            if ($score['risk_level'] === 'MEDIUM') {
                return 'FLAG - Medium risk duplicate detected';
            }
        }
        
        return 'APPROVE - No significant duplicates detected';
    }
}
