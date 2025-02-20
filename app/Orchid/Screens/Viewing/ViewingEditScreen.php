<?php

namespace App\Orchid\Screens\Viewing;

use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\Setting;
use App\Models\User;
use App\Orchid\Layouts\ConsultantContactFormLayout;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use Illuminate\Http\Request;
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

class ViewingEditScreen extends Screen
{
    public $viewing; // Record model
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
            'viewing' => $record,
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
        return isset($this->viewing->id) ? __('Edit Viewing') : __('Add Viewing');
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
                ->confirm(__('Are you sure you want to delete this viewing record?'))
                ->canSee(isset($this->viewing->id) && canUserDelete()),
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
        $canSelect = !authUserInRole('bireysel-danisman');
        return [
            // Call Scripts for listening selectbox changes
            Layout::view('partials/ConsultantContactScript'),

            Layout::modal('addContactModal', [
                Layout::view('partials/ProfilePhoto', ['edit' => false, 'contact' => null, 'contactModal' => true, 'firstCall' => true]),
                ContactFormLayout::class,
            ])->applyButton(__('Save'))->withoutCloseButton()->title(__('Add New Contact'))->rawClick(),

            Layout::split([
                $canSelect ?
                    // If user can select consultant
                    new ConsultantContactFormLayout('viewing', $this->viewing->contact_id ? $this->viewing->contact_id : null) :
                    // If user can't select consultant
                    Layout::rows([
                        Relation::make('viewing.contact_id')
                            ->fromModel(Contact::class, 'name')
                            ->applyScope('consultant')
                            ->title(__('Contact'))
                            ->required(),
                    ]),

                Layout::rows([
                    $canSelect ?
                        Select::make('viewing.portfolio_id')
                        // If user added a new contact or wants to edit the record prepared list has to setted to options
                        ->options($this->addedNewContactOrWantsToEdit ? $this->portfolioList : [])
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required()
                        :
                        Relation::make('viewing.portfolio_id')
                        ->fromModel(Portfolio::class, 'title')
                        ->applyScope('officeOrConsultant')
                        ->title(__('Portfolio'))
                        ->required(),

                    Group::make([
                        Select::make('viewing.contact_resource')
                            ->options($jsonData['contact_resources'])
                            ->title(__('Contact Resource'))
                            ->empty(__('Select')),
                        Select::make('viewing.record_level')
                            ->options($jsonData['record_levels'])
                            ->title(__('Severity Level'))
                            ->empty(__('Select')),
                    ]),
                    Group::make([
                        Select::make('viewing.record_result')
                            ->options($jsonData['demonstration_results'])
                            ->title(__('Viewing Result'))
                            ->empty(__('Select')),
                        Input::make('viewing.budget')
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
                            ->title(__("Caller's Budget")),
                    ]),
                    Group::make([
                        Input::make('viewing.price_offer')
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
                        DateTimer::make('viewing.record_date')
                            ->value($this->viewing?->record_date ? $this->viewing->record_date : date('Y-m-d'))
                            ->title(__('Interview Date'))
                            ->format('Y-m-d'),
                    ]),
                    TextArea::make('viewing.notes')
                        ->title(__('Interview Notes'))
                        ->rows(5)
                        ->maxlength(200)
                ])
            ])->ratio('50/50')
        ];
    }

    public function save(Request $request)
    {
        if (isset($this->viewing->id)) {
            $record = Record::find($this->viewing->id);
        } else {
            $record = new Record();
        }
        $viewingId = RecordType::where('name', 'Yer Gösterme')->first()->id;
        $record->record_type_id = $viewingId;
        if (authUserInRole('bireysel-danisman')) {
            $record->user_id = auth()->user()->id;
        } else {
            $record->user_id = $request->input('viewing.user_id');
        }
        $record->fill($request->get('viewing'))->save();

        Toast::info(__('You have successfully created a viewing record.'));

        return redirect()->route('platform.viewing.edit', $record->id);
    }

    public function delete()
    {
        $this->viewing->delete();

        Toast::info(__('You have successfully deleted the viewing.'));

        return redirect()->route('platform.viewing');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.edit',
        ];
    }
}
