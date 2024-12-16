<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Peribadi')
                ->description('Maklumat peribadi pagawai.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('sila masukkan nama anda')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('nric')
                    ->label('No Kad Pengenalan')
                    ->placeholder('sila masukkan no ic anda')
                    ->required()
                    ->numeric()
                    ->maxValue('12')
                    ->helperText(New HtmlString('* Tanpa (-) <strong>Contoh: 931104086159</strong>')),
                ])->columns(2),
                Forms\Components\DatePicker::make('dob')
                    ->label('Tarikh Lahir'),

                Forms\Components\Select::make('gender')
                    ->label('Jantina')
                    ->placeholder('-- pilih jantina --')
                    ->options([
                        'lelaki' => 'lelaki',
                        'wanita' => 'wanita',
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('No Telefon')
                    ->tel()
                    ->required()
                    ->maxLength(12),
                Forms\Components\TextArea::make('address')
                    ->label('Alamat')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('postcode')
                    ->label('Poskod')
                    ->numeric(),
                Forms\Components\TextInput::make('state')
                    ->label('Negeri')
                    ->maxLength(255),

                Forms\Components\Section::make('Tetapan Pegawai')
                    ->description('Tetapan akaun pegawai.')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->maxLength(255),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nric')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dob')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('postcode')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
