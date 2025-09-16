<?php

declare(strict_types=1);

namespace App\Filament\Resources\KycVerificationResource\Pages;

use App\Filament\Resources\KycVerificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKycVerification extends CreateRecord
{
    protected static string $resource = KycVerificationResource::class;
}
