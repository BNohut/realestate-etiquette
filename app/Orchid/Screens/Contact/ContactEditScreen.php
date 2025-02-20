<?php

namespace App\Orchid\Screens\Contact;

use App\Models\Contact;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ContactEditScreen extends Screen
{
    public $contact;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Contact $contact): iterable
    {
        $contact->load('attachment');
        $contact->consultant_id = $contact->user_id;

        return [
            'contact' => $contact,
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
        return __('Edit Contact') . " | " . $this->contact->name;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->class('commandbar-save-button btn')
                ->icon('save')
                ->method('createOrUpdate'),
            Button::make(__('Delete'))
                ->icon('trash')
                ->method('delete')
                ->class('btn btn-danger')
                ->confirm(__('Are you sure you want to delete this contact?'))
                ->canSee(canUserDelete())
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
            Layout::view('partials/ProfilePhoto', ['edit' => true, 'contact' => $this->contact, 'contactModal' => true, 'firstCall' => true]),
            ContactFormLayout::class,
        ];
    }

    public function createOrUpdate(Contact $contact, Request $request)
    {
        $request->validate([
            'contact.name' => 'required',
            'contact.phone' => 'required',
            'contact.email' => 'required|email|unique:contacts,email' . $contact->id ? ',' . $contact->id : '',
            'contact.address' => 'required',
            'contact.province_id' => 'required',
        ], [
            'contact.name.required' => __('Name is required'),
            'contact.phone.required' => __('Phone is required'),
            'contact.email.email' => __('Email is not valid'),
            'contact.email.required' => __('Email is required'),
            'contact.email.unique' => __('Email is already taken'),
            'contact.address.required' => __('Address is required'),
            'contact.province_id.required' => __('City is required'),
        ]);

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
            //If contact has old avatar, delete it
            if ($contact->attachment()->first()) {
                $contact->attachment[0]->delete();
            }
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

        Toast::info(__('You have successfully updated a contact.'));

        return redirect()->route('platform.contact');
    }

    public function delete(Contact $contact)
    {
        if ($contact->attachment()->first()) {
            $contact->attachment[0]->delete();
        }
        $contact->delete();

        Toast::info(__('The contact has been deleted'));

        return redirect()->route('platform.contact');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.contacts.edit',
        ];
    }
}
