<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\Office;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\SimpleMDE;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Group::make([
                Input::make('user.name')
                    ->type('text')
                    ->max(255)
                    ->required()
                    ->title(__('First Name')),

                Input::make('user.last_name')
                    ->type('text')
                    ->max(255)
                    ->required()
                    ->title(__('Last Name')),

                Input::make('user.user_name')
                    ->type('text')
                    ->max(255)
                    ->title(__('User Name')),
            ]),

            Group::make([
                Input::make('user.email')
                    ->type('email')
                    ->required()
                    ->title(__('Email')),

                Input::make('user.phone')
                    ->type('tel')
                    ->title(__('Phone Number'))
                    ->mask('(999) 999-9999'),
            ]),

            Input::make('user.url')
                ->type('text')
                ->title(__('External URL'))
                ->popover(__('External Profile Link') . " (" . __('Remax, Sahibinden, Linked-In etc.') . " )")
                ->placeholder('https://www.')
                ->style('max-width: 100% !important;'),

            Matrix::make('user.json')
                ->title(__('Social Media'))
                ->columns(['Platform', 'Link'])
                ->fields([
                    'Platform' => Select::make()->options([
                        "Facebook" => "Facebook",
                        "Instagram" => "Instagram",
                        "Linked-In" => "Linked-In",
                        "Twitter" => "Twitter"
                    ])->empty(__('Select Platform')),
                    'Link' => Input::make()
                ]),
            Group::make([
                Switcher::make('user.visibility')
                    ->sendTrueOrFalse()
                    ->title(__('Visibility'))
                    ->popover(__('This profile is visible to other users when the switch is on'))
                    ->class('form-check-input')->style('width: 3rem; height: 1.5rem; margin-top: .5rem;'),

                Relation::make('user.office_id')
                    ->fromModel(Office::class, 'name')
                    ->title(__('Office'))
                    ->help(__('Select the office if the user is a office consultant'))
                    ->canSee(authUserInRole(['super-yonetici', 'yonetici'])),
            ]),
            SimpleMDE::make('user.about_me')->title(__('About Me'))
        ];
    }
}
