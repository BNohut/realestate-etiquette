<?php

namespace App\Orchid\Screens\Sale;

use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\User;
use App\Orchid\Layouts\ConsultantContactFormLayout;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SaleEditScreen extends Screen
{
    public $sale;
    public $portfolioList;
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
            'sale' => $record,
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
        return isset($this->sale->id) ? 'Sale Closing Edit' : 'Sale Closing Create';
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
                ->confirm(__('Are you sure you want to delete this sale closing record?'))
                ->canSee(isset($this->sale->id) && canUserDelete()),
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
                            Relation::make('sale.contact_id')
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
                                ->action(route('platform.call.edit', ['method' => 'saveContact', 'record' => 0, 'screenName' => 'sale'])),
                        ]),
                    ]) :
                    // If user can select consultant
                    new ConsultantContactFormLayout('sale', $this->sale->contact_id ? $this->sale->contact_id : null),

                Layout::rows([
                    !$canSelect ?
                        Relation::make('sale.portfolio_id')
                        ->fromModel(Portfolio::class, 'title')
                        ->applyScope('officeOrConsultant')
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required()
                        :
                        Select::make('sale.portfolio_id')
                        // If user added a new contact or wants to edit the record prepared list has to setted to options
                        ->options($this->addedNewContactOrWantsToEdit ? $this->portfolioList : [])
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required(),
                    Input::make('sale.prepayment')
                        ->title(__('Taken Deposit'))
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
                        ->required(),
                    Input::make('sale.sales_price')
                        ->title(__('Total Sale Price'))
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
                        ->required(),
                    DateTimer::make('sale.record_date')
                        ->value($this->sale?->record_date ? $this->sale->record_date : date('Y-m-d'))
                        ->title(__('Record Date'))
                        ->format('Y-m-d'),

                ])
            ])->ratio('50/50')
        ];
    }

    public function save(Request $request)
    {
        if (isset($this->sale->id)) {
            $record = $this->sale;
        } else {
            $record = new Record();
            $saleTypeId = RecordType::where('name', 'Satış Kapama')->first()->id;
            $record->record_type_id = $saleTypeId;
        };
        if ($request->has('sale.user_id')) {
            $record->user_id = $request->input('sale.user_id');
        } else {
            $record->user_id = auth()->user()->id;
        }
        $record->fill($request->get('sale'))->save();

        Toast::info(isset($this->sale->id) ? __('You have successfully updated a sale closing record.') : __('You have successfully created a sale closing record.'));

        return redirect()->route('platform.sale.edit', $record);
    }

    public function delete()
    {
        $this->sale->delete();

        Toast::info(__('You have successfully deleted a sale closing record.'));

        return redirect()->route('platform.sale');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.edit',
        ];
    }
}
