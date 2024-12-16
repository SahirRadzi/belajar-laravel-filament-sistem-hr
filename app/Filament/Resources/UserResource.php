<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Notifications\Notification;

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
                    ->helperText(New HtmlString('* Tanpa (-) <strong>Contoh: 931104086159</strong>'))
                    ->live(debounce: 1500)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('generate')
                            ->label('Auto isi tarikh lahir & jantina')
                            ->icon('heroicon-m-clipboard')
                            ->action(function (Set $set, $state, ?Model $record){
                                $birthday = static::extractBirthday($state);
                                $set('dob',$birthday);
                                $set('gender', static::genderFromKP($state));
                                $set('password',$state);

                                Notification::make()
                                    ->title('Maklumat telah disalin')
                                    ->success()
                                    ->send();

                            })
                    )->maxLength(12),

                    Forms\Components\DatePicker::make('dob')
                    ->live(onBlur: true)
                    ->label('Tarikh Lahir')
                    ->helperText(function ($state){
                        return \Carbon\Carbon::parse($state)->diffForHumans([
                            'parts' => 2,
                            'join' => true,
                            'skip' => 'week',
                        ]);
                    }),

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
                    ->numeric()
                    ->maxLength(5),
                Forms\Components\TextInput::make('state')
                    ->label('Negeri')
                    ->maxLength(255),
                ])->columns(2),


                Forms\Components\Section::make('Tetapan Pegawai')
                    ->description('Tetapan akaun pegawai.')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-m-envelope'),
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

    public static function extractBirthday($number)
    {
        // Extract the first six digits
        $year = substr($number, 0, 2); // e.g. 93
        $month = substr($number, 2, 2); // e.g. 11
        $day = substr($number, 4, 2); // e.g. 04

        // Determine the century of the year
        $currentYear = date('Y');
        $currentCentury = intval(substr($currentYear, 0, 2)) * 100;

        // If the extracted year is greater than the last 2 digits of the current year, it's from 1900s
        $fullYear = $year > substr($currentYear, 2, 2)
            ? ($currentCentury - 100) + intval($year) // 1900s
            : $currentCentury + intval($year);        // 2000s

        // Validate the date, if it's not a valid date return null
        if(!checkdate(intval($month), intval($day), $fullYear)) {
            return null;
        }

        // Create and return the formatted date if valid
        return sprintf('%04d-%02d-%02d', $fullYear, $month, $day);
    }

    private static function genderFromKP($id)
    {
        // Get the last digits of the ID
        $lastDigit = (int) substr($id, -1); // Cast to integer

        // Check if the last digit is odd or even number
        if($lastDigit % 2 == 0){
            return 'wanita'; //wanita
        }else{
            return 'lelaki'; //lelaki
        }
    }
}
