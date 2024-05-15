<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BasePage;
use Filament\Support\Enums\Alignment;

class EditProfile extends BasePage
{
    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\Section::make('Informações Pessoais')
                            ->description('Seus dados de usuário')
                            ->icon('heroicon-o-user-circle')
                            ->aside()
                            ->columns(4)
                            ->schema([
                                Forms\Components\Group::make()
                                    ->columnSpan(3)
                                    ->schema([
                                        $this->getNameFormComponent(),
                                        $this->getEmailFormComponent(),
                                    ]),

                                Forms\Components\FileUpload::make('avatar')
                                    ->label('Avatar')
                                    ->avatar()
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorMode(2)
                                    ->directory('users'),
                            ]),

                        Forms\Components\Section::make('Segurança')
                            ->description('Seus dados de segurança')
                            ->icon('heroicon-o-lock-closed')
                            ->aside()
                            ->schema([
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ]),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data'),
            ),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }
}
