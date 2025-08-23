<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\KycVerification;

class CreateChatterUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:chatter-user';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a test chatter user with email and KYC verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'chatter@test.com';
        
        // Create or find the chatter user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Chatter',
                'email' => $email,
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'user_type_id' => 1, // chatter type
                'phone_number' => '+1234567890',
                'timezone' => 'UTC',
                'available_for_work' => true,
                'hourly_rate' => 25.00,
                'preferred_currency' => 'USD'
            ]);
            $this->info('Created new chatter user');
        } else {
            $user->update([
                'email_verified_at' => now(),
                'user_type_id' => 1,
                'phone_number' => '+1234567890',
                'timezone' => 'UTC',
                'available_for_work' => true,
                'hourly_rate' => 25.00,
                'preferred_currency' => 'USD'
            ]);
            $this->info('Updated existing chatter user');
        }

        // Create or update KYC verification
        $kycVerification = $user->kycVerification()->first();
        if (!$kycVerification) {
            $kycVerification = KycVerification::create([
                'user_id' => $user->id,
                'id_document_type' => 'passport',
                'id_document_number' => 'P12345678',
                'first_name' => 'Test',
                'last_name' => 'Chatter',
                'date_of_birth' => '1990-01-01',
                'phone_number' => '+1234567890',
                'address' => '123 Test Street',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'United States',
                'status' => 'approved',
                'submitted_at' => now(),
                'reviewed_at' => now(),
                'reviewed_by' => 1 // Admin user ID
            ]);
            $this->info('Created KYC verification');
        } else {
            $kycVerification->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => 1
            ]);
            $this->info('Updated KYC verification to approved');
        }

        $this->newLine();
        $this->info('🎯 Chatter Test Credentials:');
        $this->table(['Field', 'Value'], [
            ['Email', $user->email],
            ['Password', 'password123'],
            ['User Type', $user->userType->name ?? 'N/A'],
            ['Email Verified', $user->hasVerifiedEmail() ? 'Yes' : 'No'],
            ['KYC Status', $kycVerification->status],
            ['Is Chatter', $user->isChatter() ? 'Yes' : 'No'],
            ['KYC Verified', $user->isKycVerified() ? 'Yes' : 'No'],
        ]);
        
        $this->newLine();
        $this->info('✅ You can now login at: http://127.0.0.1:8001/custom/login');
        $this->info('🎯 Access chatter tests at: http://127.0.0.1:8001/chatter/tests');
        
        return 0;
    }
}
