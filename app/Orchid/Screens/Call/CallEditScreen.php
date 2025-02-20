<?php

namespace App\Orchid\Screens\Call;

use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Orchid\Layouts\ConsultantContactFormLayout;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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

class CallEditScreen extends Screen
{
    public $call;
    public $portfolioList;
    // We will use that variable in portfolio select box to show the consultant's portfolios
    public $addedNewContactOrWantsToEdit;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record, Request $request): iterable
    {
        // If record exists, it means that the user wants to edit the record
        if ($record->exists) {
            $addedNewContactOrWantsToEdit = true;
        }
        // If EndUser Created A Contact, Catch Id of The New Contact & Set it to SelectBox
        if ($request->contact) {
            // If $request has contact param, that means the user created a new contact
            $addedNewContactOrWantsToEdit = true;
            // Find the contact
            $contact = Contact::find($request->contact);
            if ($contact) {
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
            // If $request has contact param, Prepare portfolio list for select box
            // If has not, layout query is dynamic. It will display relation component
            $consultantPortfolios = Portfolio::where('user_id', $contact->user_id)->select('id', 'title')->get();
            foreach ($consultantPortfolios as $portfolio) {
                $this->portfolioList[$portfolio->id] = $portfolio->title;
            }
        } else {
            if (isset($record->contact_id)) {
                $consultantPortfolios = Portfolio::where('user_id', $record->contactS->user_id)->select('id', 'title')->get();
                foreach ($consultantPortfolios as $portfolio) {
                    $this->portfolioList[$portfolio->id] = $portfolio->title;
                }
            }
        }
        return [
            'call' => $record,
            // Set addedNewContactOrWantsToEdit variable
            // If not setted before (in query), set it as false
            'addedNewContactOrWantsToEdit' => $addedNewContactOrWantsToEdit ?? false,
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
        return $this->call->exists ? __('Edit Call') : __('Add New Call');
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
                ->method('save'),
            Button::make(__('Delete'))
                ->icon('trash')
                ->method('delete')
                ->class('btn btn-danger')
                ->confirm(__('Are you sure you want to delete this call?'))
                ->canSee($this->call->id != null && canUserDelete()),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $canSelect = authUserCanSelectConsultantForRecord();
        $jsonData = json_decode(Setting::first()->config, true);
        return [
            // Call Scripts for listening selectbox changes
            Layout::view('partials/ConsultantContactScript'),

            Layout::modal('addContactModal', [
                Layout::view('partials/ProfilePhoto', ['edit' => false, 'contact' => null, 'contactModal' => true, 'firstCall' => true]),
                ContactFormLayout::class,
            ])->applyButton(__('Save'))->withoutCloseButton()->title(__('Add New Contact'))->rawClick(),

            Layout::split([
                // Prepare dynamic layout
                // If user cant select consultant
                !$canSelect ?
                    Layout::rows([
                        Group::make([
                            Relation::make('call.contact_id')
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
                                ->method('saveContact', [
                                    'screenName' => 'call'
                                ])
                        ]),
                    ]) :
                    // If user can select consultant
                    new ConsultantContactFormLayout('call', $this->call->contact_id ? $this->call->contact_id : null),

                Layout::rows([
                    !$canSelect ?
                        Relation::make('call.portfolio_id')
                        ->fromModel(Portfolio::class, 'title')
                        ->applyScope('officeOrConsultant')
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required()
                        :
                        Select::make('call.portfolio_id')
                        // If user added a new contact or wants to edit the record prepared list has to setted to options
                        ->options($this->addedNewContactOrWantsToEdit ? $this->portfolioList : [])
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required(),
                    Group::make([
                        Select::make('call.contact_resource')
                            ->options($jsonData['contact_resources'])
                            ->title(__('Contact Resource'))
                            ->empty(__('Select')),
                        Select::make('call.record_level')
                            ->options($jsonData['record_levels'])
                            ->title(__('Severity Level'))
                            ->empty(__('Select')),
                    ]),
                    Group::make([
                        Select::make('call.record_result')
                            ->options($jsonData['interview_results'])
                            ->title(__('Interview Result'))
                            ->empty(__('Select')),
                        Input::make('call.budget')
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
                            ->title(__('Budget')),
                    ]),
                    Group::make([
                        Input::make('call.price_offer')
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
                            ->title(__('Offer Price')),
                        DateTimer::make('call.record_date')
                            ->title(__('Interview Date'))
                            ->value($this->call?->record_date ? $this->call->record_date : date('Y-m-d'))
                            ->format('Y-m-d'),
                    ]),
                    TextArea::make('call.notes')
                        ->title(__('Interview Notes'))
                        ->rows(5)
                        ->maxlength(200)
                ])
            ])->ratio('50/50')
        ];
    }

    public function saveContact(Contact $contact, Request $request)
    {
        $request->validate([
            'contact.name' => 'required',
            'contact.phone' => 'required',
            'contact.email' => 'required|email',
            'contact.address' => 'required',
            'contact.province_id' => 'required',
        ], [
            'contact.name.required' => __('Name is required'),
            'contact.phone.required' => __('Phone is required'),
            'contact.email.email' => __('Email is not valid'),
            'contact.email.required' => __('Email is required'),
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
            $parameters = [
                'contact' => $contact->id
            ];
        } else {
            $contact->fill($request->get('contact'))->save();
            $parameters = [
                'contact' => $contact->id
            ];
        }
        Toast::info(__('You have successfully updated a contact.'));

        return redirect()->route('platform.' . $request->screenName . '.edit', $parameters);
    }

    public function save(Request $request)
    {
        $callTypeId = RecordType::where('name', 'Çağrı')->first()->id;
        $exists = $this->call->exists ? true : false;
        $this->call->record_type_id = $callTypeId;
        if ($request->has('call.user_id')) {
            $this->call->user_id = $request->input('call.user_id');
        } else {
            $this->call->user_id = auth()->user()->id;
        }
        $this->call->fill($request->get('call'))->save();

        if ($exists) {
            Toast::info(__('You have updated the call record'));
        } else {
            if (!authUserInRole(['ofis-danismani', 'bireysel-danisman'])) {
                //Send Notification to Following Consultant
                Notification::send(
                    User::find($this->call->user_id),
                    new SystemNotification(
                        "Adınıza çağrı kaydı kaydedildi!",
                        auth()->user()->name . " " . auth()->user()->last_name . " sizin adınıza bir çağrı kaydı oluşturdu.",
                        'platform.call'
                    )
                );
            }
            Toast::info(__('You have created a new call record'));
        }

        return redirect()->route('platform.call.edit', $this->call->id);
    }

    public function delete()
    {
        $this->call->delete();

        Toast::info(__('You have deleted the call record'));

        return redirect()->route('platform.call');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.edit'
        ];
    }
}
