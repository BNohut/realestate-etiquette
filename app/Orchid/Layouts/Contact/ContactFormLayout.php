<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Contact;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;
use App\Models\Province;
use App\Models\User;
use Orchid\Screen\Fields\Relation;

class ContactFormLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $canSelect = authUserCanSelectConsultantForRecord();
        return [
            Relation::make('contact.consultant_id')
                ->fromModel(User::class, 'name')
                ->title(__('Consultant'))
                ->applyScope('consultant')
                ->displayAppend('full')
                ->required($canSelect)
                ->canSee($canSelect),
            Input::make('contact.name')->title(__('Full Name'))->id('name')->required(),
            Input::make('contact.phone')->type('tel')->title(__('Phone Number'))->mask('(999) 999-9999')->id('phone')->required(),
            Input::make('contact.email')->type('email')->title(__('Email Address'))->mask('***@***.***')->id('email'),
            Select::make('contact.gender')->options(['Female' => __('Female'), 'Male' => __('Male')])->empty(__('Select'))->id('gender')->title(__('Gender')),
            Select::make('contact.province_id')
                ->fromModel(Province::class, 'name')
                ->value($this->query->get('authUser')->province_id ?? null)
                ->empty(__('Select'))
                ->title(__('Province')),
            TextArea::make('contact.address')->title(__('Address'))->id('address'),
            Input::make('contact.profession')->title(__('Profession'))->id('profession'),
            TextArea::make('contact.details')->title(__('Details'))->id('details'),
        ];
    }
}
