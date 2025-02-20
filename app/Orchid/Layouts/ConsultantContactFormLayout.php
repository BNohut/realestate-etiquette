<?php

namespace App\Orchid\Layouts;

use App\Models\Contact;
use App\Models\User;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class ConsultantContactFormLayout extends Rows
{
    public $page;
    public $contact;
    public $consultant;
    public $contactList;

    public function __construct($page, $contact)
    {
        // Get the page name for usage in the field names
        $this->page = $page;
        // If Request Has Contact ID, 
        // That means end-user added new contact to system
        // Set the contact and consultant
        if ($contact) {
            $this->contact = $contact;
            $this->consultant = Contact::find($contact)->user_id;
            // We should prepare the Contacts of Consultant List for the Select Field below
            // it should be like "id" => "name"
            $this->contactList = [];
            $consultantContacts = Contact::where('user_id', $this->consultant)->select('id', 'name')->get();
            foreach ($consultantContacts as $contact) {
                $this->contactList[$contact->id] = $contact->name;
            }
        }
    }
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Relation::make($this->page . '.user_id') // make the field name dynamic
                ->value($this->consultant ? $this->consultant : null) // If end user added new contact, set the consultant
                ->fromModel(User::class, 'name')
                ->title(__('Consultant'))
                ->displayAppend('full') // Display the full name of consultant from model
                ->applyScope('consultant') // Apply the scope to get consultants depends on the auth user role
                ->required()
                ->id('selectConsultant'),
            Group::make([
                Select::make($this->page . '.contact_id') // make the field name dynamic
                    ->value($this->contact ? $this->contact : null) // If end user added new contact, set the contact
                    ->options($this->contact ? $this->contactList : []) // If end user added new contact, we should fill the field
                    ->title(__('Contact'))
                    ->id('selectContact')
                    ->required()
                    ->canSee($this->page != 'marketing'),
                ModalToggle::make() // Add new contact button
                    ->icon('plus')
                    ->class('btn btn-outline-info addContactButton')
                    ->modal('addContactModal')
                    ->id('addContactButton')
                    ->style($this->page == 'call' ||
                        $this->page == 'fsbo' ||
                        $this->page == 'viewing' ||
                        $this->page == 'customer' ||
                        $this->page == 'sale' ||
                        $this->page == 'deed' ? 'width: 50px' : '')
                    ->action(route('platform.call.edit', ['method' => 'saveContact', 'record' => 0, 'screenName' => $this->page]))
                    ->canSee($this->page != 'marketing'),
            ]),
            Select::make($this->page . '.portfolio_id')
                ->title(__('Portfolio'))
                ->empty(__('Select'))
                ->required()
                ->id('selectPortfolio')
                ->canSee($this->page == 'marketing')
        ];
    }
}
