<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Rating;

class SyncProfileRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:profile-ratings {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync profile ratings aggregates with actual review data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in dry-run mode - no changes will be made.');
        }
        
        $this->info('Starting profile ratings sync...');
        
        // Get all users with profiles
        $users = User::whereHas('userProfile')->with('userProfile')->get();
        
        $totalUsers = $users->count();
        $updatedCount = 0;
        $errorCount = 0;
        
        $this->info("Processing {$totalUsers} user profiles...");
        
        $progressBar = $this->output->createProgressBar($totalUsers);
        $progressBar->start();
        
        foreach ($users as $user) {
            try {
                // Get public ratings for this user
                $ratings = Rating::where('rated_id', $user->id)
                    ->where('is_public', true)
                    ->get();
                
                $totalRatings = $ratings->count();
                $averageRating = $totalRatings > 0 ? round($ratings->avg('overall_rating'), 2) : 0;
                
                $profile = $user->userProfile;
                $needsUpdate = false;
                $changes = [];
                
                // Check if updates are needed
                if ($profile->total_ratings != $totalRatings) {
                    $changes[] = "total_ratings: {$profile->total_ratings} -> {$totalRatings}";
                    $needsUpdate = true;
                }
                
                if ($profile->average_rating != $averageRating) {
                    $changes[] = "average_rating: {$profile->average_rating} -> {$averageRating}";
                    $needsUpdate = true;
                }
                
                if ($needsUpdate) {
                    if ($dryRun) {
                        $this->line("\nWould update user {$user->id} ({$user->name}): " . implode(', ', $changes));
                    } else {
                        $profile->update([
                            'total_ratings' => $totalRatings,
                            'average_rating' => $averageRating,
                        ]);
                    }
                    $updatedCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("\nError processing user {$user->id}: " . $e->getMessage());
                $errorCount++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine();
        
        if ($dryRun) {
            $this->info("Dry run completed. {$updatedCount} profiles would be updated.");
        } else {
            $this->info("Sync completed. {$updatedCount} profiles updated.");
        }
        
        if ($errorCount > 0) {
            $this->warn("{$errorCount} errors occurred during processing.");
        }
        
        return 0;
    }
}
