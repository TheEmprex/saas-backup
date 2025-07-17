<?php

namespace App\Filament\Resources\EarningsVerificationResource\Pages;

use App\Filament\Resources\EarningsVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEarningsVerification extends ViewRecord
{
    protected static string $resource = EarningsVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
