<?php

namespace App\Orchid\Screens\Deed;

use App\Models\Contact;
use App\Models\Office;
use App\Models\Portfolio;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\User;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use Illuminate\Http\Request;
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
use App\Notifications\SystemNotification;
use App\Orchid\Layouts\ConsultantContactFormLayout;
use Illuminate\Support\Facades\Notification;

class DeedEditScreen extends Screen
{
    public $deed;
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
            'deed' => $record,
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
        return isset($this->deed->id) ? 'Edit Deed Sale-Rent Process' : 'Create Deed Sale-Rent Process';
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
                ->confirm(__('Are you sure you want to delete this deed sale-rent process record?'))
                ->canSee(isset($this->deed->id) && canUserDelete()),
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
                            Relation::make('deed.contact_id')
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
                                ->action(route('platform.call.edit', ['method' => 'saveContact', 'record' => 0, 'screenName' => 'deed'])),
                        ]),
                    ]) :
                    // If user can select consultant
                    new ConsultantContactFormLayout('deed', $this->deed->contact_id ? $this->deed->contact_id : null),

                Layout::rows([
                    !$canSelect ?
                        Relation::make('deed.portfolio_id')
                        ->fromModel(Portfolio::class, 'title')
                        ->applyScope('officeOrConsultant')
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required()
                        :
                        Select::make('deed.portfolio_id')
                        // If user added a new contact or wants to edit the record prepared list has to setted to options
                        ->options($this->addedNewContactOrWantsToEdit ? $this->portfolioList : [])
                        ->title(__('Portfolio'))
                        ->id('selectPortfolio')
                        ->required(),
                    Input::make('deed.sales_price')
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
                    Select::make('deed.activity_type')
                        ->options(["Satıldı" => __('Sold'), "Kiralandı" => __('Rented')])
                        ->title(__('Operation Type'))
                        ->empty(__('Select'))
                        ->required(),
                    DateTimer::make('deed.record_date')
                        ->title(__('Operation Date'))
                        ->value($this->deed?->record_date ? $this->deed->record_date : date('Y-m-d'))
                        ->format('Y-m-d'),

                ])
            ])->ratio('50/50')
        ];
    }

    public function save(Request $request)
    {
        if (isset($this->deed->id)) {
            $record = $this->deed;
        } else {
            $record = new Record();
            $deedTypeId = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $record->record_type_id = $deedTypeId;
        };

        if ($request->has('deed.user_id')) {
            $record->user_id = $request->input('deed.user_id');
            if (!isset($this->deed->id)) {
                if (User::find($record->user_id)->inRole('bireysel-danisman')) {
                    $record->approved_at = now();
                } else {
                    // Find Manager of Office
                    $officeId = User::find($record->user_id)->office_id;
                    $office = Office::find($officeId);
                    //Sent Notification to Office Manager
                    Notification::send(
                        User::find($office->user_id),
                        new SystemNotification(
                            "Tapu Satış-Kiralama Kaydı Bilgisi Alındı!",
                            User::find($record->user_id)->getFullNameAttribute() . " tapu satış-kiralama kaydı oluşturdu.",
                            'platform.office.deed'
                        )
                    );
                }
            }
        } else {
            $record->user_id = auth()->user()->id;
            // Find Manager of Office
            $officeId = auth()->user()->office_id;
            $office = Office::find($officeId);
            //Sent Notification to Office Manager
            Notification::send(
                User::find($office->user_id),
                new SystemNotification(
                    "Tapu Satış-Kiralama Kaydı Bilgisi Alındı!",
                    $request->user()->getFullNameAttribute() . " tapu satış-kiralama kaydı oluşturdu.",
                    'platform.office.deed'
                )
            );
        }

        $record->fill($request->get('deed'))->save();

        Toast::info(isset($this->deed->id) ? __('You have successfully updated a deed sale-rent process') : __('You have successfully created a deed sale-rent process.'));

        return redirect()->route('platform.deed.edit', $record);
    }

    public function delete()
    {
        $this->deed->delete();

        Toast::info(__('You have successfully deleted a deed sale-rent process.'));

        return redirect()->route('platform.deed');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.edit',
        ];
    }
}
