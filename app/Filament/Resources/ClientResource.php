<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('Label')
                ->tabs([
                    Tabs\Tab::make('Personal info')
                        ->schema([
                            TextEntry::make('first_name')
                                ->label('Name')
                                ->formatStateUsing(fn(string $state, $record): string => $record->first_name . " " . $record->last_name),

                            TextEntry::make('phone'),
                            TextEntry::make('mobile'),

                            TextEntry::make('email')
                                ->copyable(),

                            ImageEntry::make('photo'),
                            TextEntry::make('linkedin')
                                ->suffixAction(
                                    Action::make('openLinkedin')
                                        ->icon('heroicon-m-clipboard')
                                        ->url(fn($record) => $record->linkedin)
                                ),

                            TextEntry::make('active')
                                ->badge()
                                ->color(fn(bool $state): string => match ($state) {
                                    false => 'gray',
                                    true => 'success'
                                })
                        ]),
                    Tabs\Tab::make('Business info')
                        ->schema([
                            TextEntry::make('company'),
                            TextEntry::make('title'),
                            TextEntry::make('role'),
                            TextEntry::make('company_website'),
                            TextEntry::make('business_details'),
                            TextEntry::make('business_type'),
                            TextEntry::make('company_size'),
                            TextEntry::make('company_size'),
                            TextEntry::make('temperature'),

                        ]),
                    Tabs\Tab::make('Notes')
                        ->schema([
                            TextEntry::make('notes'),
                            TextEntry::make('referrals'),

                        ]),
                ])->columnSpanFull()


        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'md' => 3,
                ])
                    ->schema([
                        Section::make()->schema([
                            Section::make('Personal Info')
                                ->description("Client's personal information")
                                ->collapsible()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('first_name')
                                        ->required()
                                        ->string()
                                        ->minLength(2)
                                        ->maxLength(255),
                                    TextInput::make('last_name')
                                        ->required()
                                        ->string()
                                        ->maxLength(255),
                                    TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('phone')
                                        ->tel()
                                        ->minLength(2)
                                        ->maxLength(255),
                                    TextInput::make('mobile')
                                        ->tel()
                                        ->minLength(2)
                                        ->maxLength(255),
                                    Toggle::make('active')
                                        ->required(),
                                ]),
                            Section::make('Business Info')
                                ->description("Client's business information")
                                ->collapsible()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('title')
                                        ->string()
                                        ->minLength(2)
                                        ->maxLength(255),
                                    TextInput::make('company')
                                        ->string()
                                        ->minLength(2)
                                        ->maxLength(255),
                                    TextInput::make('role')
                                        ->string()
                                        ->minLength(2)
                                        ->maxLength(255),

                                    TextInput::make('business_type')
                                        ->string()
                                        ->minLength(2)
                                        ->maxLength(255),
                                    TextInput::make('company_website'),
                                    TextInput::make('linkedin'),
                                    RichEditor::make('business_details'),

                                    FileUpload::make('photo')
                                        ->image()
                                        ->disk('public'),
                                    Select::make('company_size')
                                        ->options([
                                            'small' => 'Small',
                                            'mid' => 'Medium',
                                            'big' => 'Large'
                                        ]),
                                    Select::make('temperature')
                                        ->options([
                                            'cold' => 'Cold',
                                            'warm' => 'Warm',
                                            'hot' => 'Hot'
                                        ]),

                                ]),
                        ]),
                        Section::make('Other Info')
                            ->description("Client's other information")
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                TextInput::make('referrals'),
                                TextInput::make('notes'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo'),
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable(),
                // TextColumn::make('email')
                //     ->searchable(),
                // TextColumn::make('phone')
                //     ->searchable(),
                // TextColumn::make('mobile')
                //     ->searchable(),
                // TextColumn::make('title')
                //     ->searchable(),
                TextColumn::make('company')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('role')
                //     ->searchable(),
                TextColumn::make('company_website')
                    ->toggleable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('business_type')
                //     ->searchable(),
                SelectColumn::make('company_size')
                    ->options([
                        'small' => 'Small',
                        'mid' => 'Medium',
                        'big' => 'Large'
                    ]),
                SelectColumn::make('temperature')
                    ->options([
                        'cold' => 'Cold',
                        'warm' => 'Warm',
                        'hot' => 'Hot'
                    ]),


                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}/view'),
        ];
    }
}