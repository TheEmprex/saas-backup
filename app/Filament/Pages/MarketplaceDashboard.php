<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\MarketplaceStatsWidget;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Auth;

class MarketplaceDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.marketplace-dashboard';

    protected static ?string $title = 'Marketplace Analytics';

    protected static ?int $navigationSort = 1;

    protected function getHeaderWidgets(): array
    {
        return [
            MarketplaceStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_marketplace')
                ->label('Back to Marketplace')
                ->url(route('marketplace.dashboard'))
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->size(ActionSize::Small),

            Action::make('logout')
                ->label('Disconnect Admin')
                ->action(function () {
                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->route('marketplace.index');
                })
                ->icon('heroicon-m-arrow-right-start-on-rectangle')
                ->color('danger')
                ->size(ActionSize::Small)
                ->requiresConfirmation()
                ->modalHeading('Disconnect from Admin Panel')
                ->modalDescription('This will log you out completely and redirect you to the marketplace. You will need to log in again.')
                ->modalSubmitActionLabel('Yes, Disconnect')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
