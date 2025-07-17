<?php

namespace App\Filament\Resources\EarningsVerificationResource\Pages;

use App\Filament\Resources\EarningsVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEarningsVerification extends EditRecord
{
    protected static string $resource = EarningsVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
