<?php

namespace App\Orchid\Screens\Customer;

use App\Models\Contact;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\Setting;
use App\Models\State;
use App\Models\User;
use App\Orchid\Layouts\ConsultantContactFormLayout;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use App\Orchid\Layouts\ProvinceStateNeighborhood;
use Illuminate\Http\Request;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CustomerEditScreen extends Screen
{
    public $customer;
    public $province;
    public $state;
    public $neighborhoods;
    public $neighborhood;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $record = Record::find($request->record) ? Record::find($request->record) : new Record();

        // If EndUser Created A Contact, Catch Id of The New Contact & Set it to SelectBox
        if ($request->contact) {
            $contact = Contact::find($request->contact);
            // Check if the contact belongs to the auth user if the user is in 'ofis-danismani' or 'bireysel-danisman' roles
            // We made this condition because of the end-user can't see the contacts of other consultants
            // And url can be changed by the end-user easly
            if (authUserInRole(['ofis-danismani', 'bireysel-danisman'])) {
                if ($contact && $contact->user_id == auth()->user()->id) {
                    $record->contact_id = $contact->id;
                } else {
                    //TODO: Clear Request
                }
                // Otherwise, set the contact to the record
            } else {
                $record->contact_id = $contact->id;
            }
        }
        $neighborhoodOptions = [];
        $authUser = auth()->user();
        if (isset($record->province_id) || isset($authUser->province_id)) {
            $neighborhoodsList = explode(", ", State::find(isset($record->state_id) ? $record->state_id : $authUser->state_id)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }
        return [
            'customer' => $record,
            'province' => isset($record->province_id) ? $record->province_id : $authUser->province_id,
            'state' => isset($record->state_id) ? $record->state_id : $authUser->state_id,
            'neighborhood' => $record->neighborhood,
            'neighborhoods' => $neighborhoodOptions,
            'authUser' => $authUser,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return isset($this->customer->id) ? __('Edit Customer Record') : __('Add Customer Record');
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
                ->confirm(__('Are you sure you want to delete this customer record?'))
                ->canSee(isset($this->customer->id) && canUserDelete())
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $jsonData = json_decode(Setting::first()->config, true);
        $groupList = $jsonData['portfolio_groups'];
        $newList = [];
        $variationList = [];
        foreach ($groupList as $key => $value) {
            $newList[$key] = $key;
        }

        if (isset($this->customer->id)) {
            $variationList = $jsonData['portfolio_groups'][$this->customer->portfolio_group];
        }
        $canSelect = authUserCanSelectConsultantForRecord();
        return [
            Layout::view('partials/ConsultantContactScript'),

            Layout::modal('addContactModal', [
                Layout::view('partials/ProfilePhoto', ['edit' => false, 'contact' => null, 'contactModal' => true, 'firstCall' => true]),
                ContactFormLayout::class,
            ])->applyButton(__('Save'))->withoutCloseButton()->title(__('Add New Contact'))->rawClick(),

            Layout::split([
                !$canSelect ?
                    Layout::rows([
                        Group::make([
                            Relation::make('customer.contact_id')
                                ->fromModel(Contact::class, 'name')
                                ->applyScope('consultant')
                                ->title(__('Contact'))
                                ->id('selectContact')
                                ->required(),
                            ModalToggle::make()
                                ->icon('plus')
                                ->class('btn btn-outline-info addContactButton')
                                ->modal('addContactModal')
                                ->id('addContactButton')
                                ->action(route('platform.call.edit', ['method' => 'saveContact', 'record' => 0, 'screenName' => 'customer'])),
                        ]),
                    ]) : new ConsultantContactFormLayout('customer', isset($this->customer->contact_id) ? $this->customer->contact_id : null),
                ProvinceStateNeighborhood::class, // Listener
            ]),


            Layout::rows([

                Group::make([
                    Select::make('customer.portfolio_type')
                        ->options($jsonData['portfolio_types'])
                        ->title(__('Portfolio Type'))
                        ->required()
                        ->empty(__('Select')),
                    Select::make('customer.portfolio_group')
                        ->options($newList)
                        ->title(__('Portfolio Group'))
                        ->empty(__('Select'))
                        ->required()
                        ->id('portfolioGroup'),
                    Select::make('customer.portfolio_variation')
                        ->options($variationList)
                        ->title(__('Variation'))
                        ->empty(__('Select Portfolio Group'))
                        ->required()
                        ->id('portfolioVariation'),
                ]),
                Group::make([
                    Input::make('customer.property_features')
                        ->title(__('Property Features')),

                    Input::make('customer.budget')
                        ->mask([
                            'alias' => 'numeric',
                            'groupSeparator' => '.',
                            'radixPoint' => ',',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'placeholder' => '0',
                            'unmaskAsNumber' => true,
                            'autoUnmask' => true,
                            'removeMaskOnSubmit' => true,
                            'suffix' => ' ₺'
                        ])
                        ->title(__('Customer Max Budget')),
                    Select::make('customer.contact_resource')
                        ->options($jsonData['contact_resources'])
                        ->title(__('Contact Resource'))
                        ->empty(__('Select')),
                ]),
                Group::make([
                    Select::make('customer.record_level')
                        ->options($jsonData['record_levels'])
                        ->title(__('Severity Level'))
                        ->empty(__('Select')),

                    DateTimer::make('customer.record_date')
                        ->title(__('Interview Date'))
                        ->value($this->customer?->record_date ? $this->customer->record_date : date('Y-m-d'))
                        ->format('Y-m-d'),
                ]),
                TextArea::make('customer.notes')
                    ->title(__('Interview Notes'))
                    ->rows(5)
                    ->maxlength(200)
                    ->style('max-width: 100%;'),
            ]),

            Layout::view('partials/PortfolioGroupVariationScript'),

        ];
    }

    public function createOrUpdate(Request $request)
    {
        if (isset($this->customer->id)) {
            $exists = true;
            $record = $this->customer;
        } else {
            $exists = false;
            $record = new Record();
        }
        if ($request->has('customer.user_id')) {
            $record->user_id = $request->input('customer.user_id');
        } else {
            $record->user_id = auth()->user()->id;
        }
        $customerTypeId = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
        $record->record_type_id = $customerTypeId;
        $record->province_id = $request->get('province');
        $record->state_id = $request->get('state');
        $record->neighborhood = $request->get('neighborhood');

        $record->fill($request->get('customer'));
        $record->save();
        Toast::info(__('You have successfully ' . ($exists ? 'updated' : 'created') . ' a customer record.'));
        return redirect()->route('platform.customer.edit', $record);
    }

    public function delete(Record $record)
    {
        $record->delete();

        return redirect()->route('platform.customer');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.edit',
        ];
    }
}
