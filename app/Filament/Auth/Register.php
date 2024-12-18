<?php
namespace App\Filament\Auth;

use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function form(form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                TextInput::make('nric')
                    ->label('No Kad Pengenalan')
                    ->placeholder('sila masukkan no ic anda')
                    ->required()
                    ->numeric()
                    ->helperText(New HtmlString('* Tanpa (-) <strong>Contoh: 831104086159</strong>'))
                    ->maxLength(12),
               TextInput::make('phone')
                    ->label('No Telefon')
                    ->tel()
                    ->required()
                    ->maxLength(12),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
