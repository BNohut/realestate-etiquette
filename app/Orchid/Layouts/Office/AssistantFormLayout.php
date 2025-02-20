<?php

namespace App\Orchid\Layouts\Office;

use App\Models\Office;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;

class AssistantFormLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        $user = $this->query->get('user');
        $userExists = false;
        $canSelectOffice = authUserInRole(['super-yonetici', 'yonetici']);
        if ($user) {
            $userExists = true;
        };
        return [
            Group::make([
                Relation::make('user.office_id')
                    ->fromModel(Office::class, 'name')
                    ->title('Office')
                    ->required($canSelectOffice)
                    ->canSee($canSelectOffice),
            ]),
            Group::make([
                Input::make('user.name')
                    ->required()
                    ->title('Name'),
                Input::make('user.last_name')
                    ->required()
                    ->title('Last Name'),
            ]),
            Group::make([
                Input::make('user.email')
                    ->required()
                    ->title('Email'),
                Input::make('user.phone')->type('tel')->title(__('Phone Number'))->mask('(999) 999-9999'),
            ]),
            Group::make([
                Input::make('assistant.password')
                    ->required(!$userExists)
                    ->title(!$userExists ? 'Password' : 'Change Password')->type('password'),
                Input::make('assistant.password_confirmation')
                    ->required(!$userExists)
                    ->title('Password Confirmation')->type('password')
            ]),
        ];
    }
}
