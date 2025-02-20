<?php

namespace App\Orchid\Screens\Contact;

use App\Orchid\Layouts\Contact\ContactFormLayout;
use App\Models\Contact;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Models\Province;
use Orchid\Attachment\File;

class ContactScreen extends Screen
{
    public $contacts;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $contacts = Contact::orderBy('name', 'asc')->get();
        }
        if (authUserInRole('ofis-yoneticisi')) {
            $contacts = Contact::join('users', 'users.id', '=', 'contacts.user_id')
                ->where('users.office_id', auth()->user()->office_id)
                ->orderBy('contacts.name', 'asc')
                ->select('contacts.*')
                ->get();
        }
        if (authUserInRole(['ofis-danismani', 'bireysel-danisman'])) {
            $contacts = Contact::where('user_id', auth()->user()->id)
                ->orderBy('contacts.name', 'asc')
                ->select('contacts.*')
                ->get();
        }
        $contacts->load('attachment');
        return [
            'contacts' => $contacts,
            'provinces' => Province::get(),
            'authUser' => auth()->user(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Contacts');
    }

    public function description(): string
    {
        return "";
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('Add'))
                ->class('commandbar-add-button btn')
                ->modal('addContactModal')
                ->method('create')
                ->icon('plus')
                ->canSee(hasUserPermission('platform.contacts.add')),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('addContactModal', [
                Layout::view('partials/ProfilePhoto', ['edit' => false, 'contact' => null, 'contactModal' => true, 'firstCall' => true]),
                ContactFormLayout::class,
            ])->applyButton(__('Save'))->title(__('Add New Contact'))->rawClick(),

            Layout::view('Contact/ContactList')
        ];
    }

    public function create(Contact $contact, Request $request)
    {
        if ($request->has('contact.consultant_id')) {
            $contact->user_id = $request->input('contact.consultant_id');
        } else {
            $contact->user_id = auth()->user()->id;
        }
        //If user uploaded an image for avatar
        //Transform Blade Input File to Orchid Attachment
        //Then Save it as Attacment
        //Else Just Save Contact
        if ($request->file('avatar')) {
            //Get Request Contact Array
            $requestContact = $request->get('contact');
            //Transform
            $file = new File($request->file('avatar'));
            $attachment = $file->load();
            $requestContact['avatar'] = $attachment;
            //Merge All To Request
            $request->merge(['contact' => $requestContact]);
            //Save
            $contact
                ->fill($request->collect('contact')->except(['avatar'])->toArray())
                ->fill(['avatar' => $attachment->id])
                ->save();
            $contact->attachment()->syncWithoutDetaching(
                $request->input('contact.avatar', [])
            );
        } else {
            $contact->fill($request->get('contact'))->save();
        }
    }

    public function permission(): ?iterable
    {
        return [
            'platform.contacts',
        ];
    }
}
