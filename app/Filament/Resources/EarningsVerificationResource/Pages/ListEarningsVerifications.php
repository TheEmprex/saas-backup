<?php

declare(strict_types=1);

namespace App\Filament\Resources\EarningsVerificationResource\Pages;

use App\Filament\Resources\EarningsVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEarningsVerifications extends ListRecords
{
    protected static string $resource = EarningsVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
