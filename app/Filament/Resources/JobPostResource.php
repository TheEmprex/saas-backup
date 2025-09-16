<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\JobPostResource\Pages;
use App\Models\JobPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobPostResource extends Resource
{
    protected static ?string $model = JobPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('requirements')
                    ->required(),
                Forms\Components\TextInput::make('min_typing_speed')
                    ->numeric(),
                Forms\Components\TextInput::make('min_english_proficiency')
                    ->numeric(),
                Forms\Components\TextInput::make('required_traffic_sources'),
                Forms\Components\TextInput::make('market')
                    ->required()
                    ->maxLength(191)
                    ->default('english'),
                Forms\Components\TextInput::make('experience_level')
                    ->required(),
                Forms\Components\TextInput::make('expected_response_time')
                    ->maxLength(191),
                Forms\Components\TextInput::make('hourly_rate')
                    ->numeric(),
                Forms\Components\TextInput::make('fixed_rate')
                    ->numeric(),
                Forms\Components\TextInput::make('rate_type')
                    ->required(),
                Forms\Components\TextInput::make('commission_percentage')
                    ->numeric(),
                Forms\Components\TextInput::make('hours_per_week')
                    ->numeric(),
                Forms\Components\TextInput::make('timezone_preference')
                    ->maxLength(191),
                Forms\Components\TextInput::make('working_hours'),
                Forms\Components\TextInput::make('contract_type'),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->required(),
                Forms\Components\Toggle::make('is_urgent')
                    ->required(),
                Forms\Components\TextInput::make('max_applications')
                    ->required()
                    ->numeric()
                    ->default(50),
                Forms\Components\TextInput::make('current_applications')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('expires_at'),
                Forms\Components\TextInput::make('tags'),
                Forms\Components\TextInput::make('views')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('benefits')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('expected_hours_per_week')
                    ->numeric(),
                Forms\Components\TextInput::make('duration_months')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_typing_speed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_english_proficiency')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('market')
                    ->searchable(),
                Tables\Columns\TextColumn::make('experience_level'),
                Tables\Columns\TextColumn::make('expected_response_time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fixed_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_type'),
                Tables\Columns\TextColumn::make('commission_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hours_per_week')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('timezone_preference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_type'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_urgent')
                    ->boolean(),
                Tables\Columns\TextColumn::make('max_applications')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_applications')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expected_hours_per_week')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_months')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobPosts::route('/'),
            'create' => Pages\CreateJobPost::route('/create'),
            'edit' => Pages\EditJobPost::route('/{record}/edit'),
        ];
    }
}
