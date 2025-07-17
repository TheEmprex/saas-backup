<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EarningsVerificationResource\Pages;
use App\Models\EarningsVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class EarningsVerificationResource extends Resource
{
    protected static ?string $model = EarningsVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Earnings Verifications';

    protected static ?int $navigationSort = 35;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Platform Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('platform_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('platform_username')
                            ->required()
                            ->maxLength(255)
                            ->prefix('@'),
                        Forms\Components\TextInput::make('monthly_earnings')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('additional_notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Verification Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->visible(fn (callable $get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('verified_at'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Files')
                    ->schema([
                        Forms\Components\FileUpload::make('earnings_screenshot_path')
                            ->label('Earnings Screenshot')
                            ->directory('earnings-verification/earnings')
                            ->disk('private')
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(5120),
                        Forms\Components\FileUpload::make('profile_screenshot_path')
                            ->label('Profile Screenshot')
                            ->directory('earnings-verification/profiles')
                            ->disk('private')
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(5120),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('platform_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform_username')
                    ->searchable()
                    ->prefix('@'),
                Tables\Columns\TextColumn::make('monthly_earnings')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('earnings_screenshot_path')
                    ->label('Earnings File')
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn (EarningsVerification $record): bool => !empty($record->earnings_screenshot_path)),
                Tables\Columns\IconColumn::make('profile_screenshot_path')
                    ->label('Profile File')
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn (EarningsVerification $record): bool => !empty($record->profile_screenshot_path)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('platform_name')
                    ->options([
                        'OnlyFans' => 'OnlyFans',
                        'Fansly' => 'Fansly',
                        'ManyVids' => 'ManyVids',
                        'Clips4Sale' => 'Clips4Sale',
                        'Chaturbate' => 'Chaturbate',
                        'Other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (EarningsVerification $record) {
                        $record->update([
                            'status' => 'approved',
                            'verified_at' => now(),
                        ]);
                    })
                    ->visible(fn (EarningsVerification $record): bool => $record->status !== 'approved'),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->label('Reason for rejection'),
                    ])
                    ->action(function (EarningsVerification $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_at' => now(),
                        ]);
                    })
                    ->visible(fn (EarningsVerification $record): bool => $record->status !== 'rejected'),
                Tables\Actions\Action::make('download_earnings')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (EarningsVerification $record) {
                        if ($record->earnings_screenshot_path && Storage::disk('private')->exists($record->earnings_screenshot_path)) {
                            return Storage::disk('private')->download($record->earnings_screenshot_path);
                        }
                    })
                    ->visible(fn (EarningsVerification $record): bool => !empty($record->earnings_screenshot_path)),
                Tables\Actions\Action::make('download_profile')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (EarningsVerification $record) {
                        if ($record->profile_screenshot_path && Storage::disk('private')->exists($record->profile_screenshot_path)) {
                            return Storage::disk('private')->download($record->profile_screenshot_path);
                        }
                    })
                    ->visible(fn (EarningsVerification $record): bool => !empty($record->profile_screenshot_path)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'approved',
                                    'verified_at' => now(),
                                ]);
                            });
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEarningsVerifications::route('/'),
            'create' => Pages\CreateEarningsVerification::route('/create'),
            'view' => Pages\ViewEarningsVerification::route('/{record}'),
            'edit' => Pages\EditEarningsVerification::route('/{record}/edit'),
        ];
    }
}
