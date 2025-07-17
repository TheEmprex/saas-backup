<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\RelationManagers;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sender_id')
                    ->relationship('sender', 'name')
                    ->required(),
                Forms\Components\Select::make('recipient_id')
                    ->relationship('recipient', 'name')
                    ->required(),
                Forms\Components\Select::make('job_post_id')
                    ->relationship('jobPost', 'title'),
                Forms\Components\TextInput::make('job_application_id')
                    ->numeric(),
                Forms\Components\Textarea::make('message_content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('attachments'),
                Forms\Components\TextInput::make('message_type')
                    ->required(),
                Forms\Components\DateTimePicker::make('read_at'),
                Forms\Components\Toggle::make('is_read')
                    ->required(),
                Forms\Components\Toggle::make('is_archived')
                    ->required(),
                Forms\Components\TextInput::make('thread_id')
                    ->maxLength(191),
                Forms\Components\TextInput::make('parent_message_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobPost.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('job_application_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('message_type'),
                Tables\Columns\TextColumn::make('read_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_archived')
                    ->boolean(),
                Tables\Columns\TextColumn::make('thread_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent_message_id')
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
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
