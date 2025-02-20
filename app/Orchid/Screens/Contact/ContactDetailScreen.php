<?php

namespace App\Orchid\Screens\Contact;

use App\Models\Contact;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class ContactDetailScreen extends Screen
{
    public $contact;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Contact $contact): iterable
    {
        return [
            'contact' => $contact
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Contact Information') . " | " . $this->contact->name;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::legend('contact', [
                Sight::make('name', __('Full Name')),
                Sight::make('email', __('E-mail')),
                Sight::make('phone', __('Phone Number')),
                Sight::make('province', __('Province'))->render(function (Contact $contact) {
                    return $contact->province->name;
                }),
                Sight::make('gender', __('Gender'))->render(function (Contact $contact) {
                    return __($contact->gender);
                }),
                Sight::make('address', __('Address')),
            ]),
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.contacts.detail',
        ];
    }
}
