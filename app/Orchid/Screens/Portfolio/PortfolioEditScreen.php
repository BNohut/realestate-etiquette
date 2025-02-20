<?php

namespace App\Orchid\Screens\Portfolio;

use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\Setting;
use App\Models\State;
use App\Models\User;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use App\Orchid\Layouts\ProvinceStateListener;
use Illuminate\Http\Request;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\SimpleMDE;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PortfolioEditScreen extends Screen
{
    public $portfolio;
    public $user;
    public $urls;
    public $contactId;
    public $province;
    public $state;
    public $neighborhoods;
    public $neighborhood;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Portfolio $portfolio, Request $request): iterable
    {
        // TRY FOR SET VALUE TO CONTACT OR CONSULTANT SELECTBOX
        // request()->contact = 1;
        // request()->consultant = 1;

        // If there is no portfolio, means no image url
        // If there is portfolio, images will loaded to urls variable
        $urls = [];

        $contactId = null;
        // $consultantId = null;

        if ($portfolio->id) {
            $portfolio->load('attachment');
            foreach ($portfolio->attachment as $image) {
                array_push($urls, $image->url());
            }
        }

        // If EndUser Created A Contact, Catch Id of The New Contact & Set it to SelectBox
        if ($request->contact) {
            $portfolio->contact_id = $request->contact;
            $contactId = $request->contact;
        }

        $authUser = auth()->user();
        $neighborhoodOptions = [];
        if (isset($portfolio->province_id) || isset($authUser->province_id)) {
            $neighborhoodsList = explode(", ", State::find(isset($portfolio->state_id) ? $portfolio->state_id : $authUser->state_id)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }
        $authUser = auth()->user();
        return [
            'portfolio' => $portfolio,
            'user' => $portfolio,
            'urls' => $urls,
            'contactId' => $contactId,
            'province' => isset($portfolio->province_id) ? $portfolio->province_id : $authUser->province_id,
            'state' => isset($portfolio->state_id) ? $portfolio->state_id : $authUser->state_id,
            'neighborhood' => isset($portfolio->neighborhood) ? $portfolio->neighborhood : $authUser->neighborhood,
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
        return $this->portfolio->title != null ? __("Edit Portfolio") . " | " . $this->portfolio->title : __('Add Portfolio');
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
                ->confirm(__('Are you sure you want to delete this portfolio?'))
                ->canSee($this->portfolio->title != null && canUserDelete()),
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

        if (isset($this->portfolio->id)) {
            $variationList = $jsonData['portfolio_groups'][$this->portfolio->portfolio_group];
        }

        $canSelect = authUserCanSelectConsultantForRecord();

        return [

            Layout::modal('addContactModal', [
                Layout::view('partials/ProfilePhoto', ['edit' => false, 'contact' => null, 'contactModal' => true, 'firstCall' => true]),
                ContactFormLayout::class,
            ])->applyButton(__('Save'))->withoutCloseButton()->title(__('Add New Contact'))->rawClick(),

            Layout::modal('mapModal', [
                Layout::rows([
                    Group::make([
                        Input::make('lat')->title(__('Latitude'))->id('modal-lat'),
                        Input::make('long')->title(__('Longitude'))->id('modal-long')
                    ]),
                ]),
                Layout::view('Portfolio/PortfolioLocation'),
            ])->closeButton(__('Save'))->withoutApplyButton()->title(__('Mark The Location Of The Portfolio'))->size(Modal::SIZE_LG),
            Layout::columns([
                Layout::rows([
                    Group::make([
                        Relation::make('portfolio.contact_id')
                            ->fromModel(Contact::class, 'name')
                            ->title(__('Select Contact'))
                            ->required()
                            ->id('selectContact'),
                        ModalToggle::make()
                            ->icon('plus')
                            ->class('btn btn-outline-info addContactButton')
                            ->modal('addContactModal')
                            ->id('addContactButton')
                            ->action(route('platform.call.edit', ['method' => 'saveContact', 'record' => 0, 'screenName' => 'portfolio'])),
                    ]),
                ]),
                Layout::rows([
                    Relation::make('portfolio.user_id')
                        ->fromModel(User::class, 'name')
                        ->title(__('Select Consultant'))
                        ->applyScope('consultant')
                        ->id('selectConsultant')
                        ->displayAppend('full')
                        ->required($canSelect),
                ])->canSee($canSelect),
            ]),
            Layout::rows([
                Group::make([
                    Input::make('portfolio.title')
                        ->title(__('Title'))
                        ->required(),
                    Input::make('portfolio.portfolio_no')
                        ->title(__('Portfolio Number'))
                        ->required(),
                ]),
                Group::make([
                    Input::make('portfolio.link')
                        ->title(__('Link'))
                        ->popover(__('External Link'))
                        ->placeholder('https://'),
                    Select::make('portfolio.portfolio_resource')
                        ->options($jsonData["portfolio_resources"])
                        ->title(__('Portfolio Resource'))
                        ->empty(__('Select'))
                        ->required(),

                ]),
                Group::make([
                    Select::make('portfolio.deed_status')
                        ->title(__('Deed Status'))
                        ->empty(__('Select'))
                        ->options($jsonData["deed_statuses"]),
                    Select::make('portfolio.portfolio_type')
                        ->options($jsonData['portfolio_types'])
                        ->title(__('Portfolio Type'))
                        ->required()
                        ->empty(__('Select')),
                    Select::make('portfolio.portfolio_group')
                        ->options($newList)
                        ->title(__('Portfolio Group'))
                        ->empty(__('Select'))
                        ->required()
                        ->id('portfolioGroup'),
                    Select::make('portfolio.portfolio_variation')
                        ->options($variationList)
                        ->title(__('Variation'))
                        ->empty(__('Select Portfolio Group'))
                        ->required()
                        ->id('portfolioVariation'),
                ]),
                Group::make([
                    Input::make('portfolio.square_total')
                        ->title(__('Gross Square Meters'))
                        ->id('square-total'),
                    Input::make('portfolio.square_net')
                        ->title(__('Net Square Meters'))
                        ->id('square-net'),
                    Input::make('portfolio.ada_no')
                        ->title('Ada No'),
                    Input::make('portfolio.parsel_no')
                        ->title('Parsel No'),
                ]),
                Group::make([
                    Input::make('portfolio.list_price')
                        ->title(__('List Price'))
                        ->mask([
                            'alias' => 'numeric',
                            'groupSeparator' => '.',
                            'radixPoint' => ',',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'unmaskAsNumber' => true,
                            'autoUnmask' => true,
                            'removeMaskOnSubmit' => true,
                            'suffix' => ' ₺'
                        ])
                        ->required(),
                    Input::make('portfolio.minimum_price')
                        ->title(__('Minimum Price'))
                        ->mask([
                            'alias' => 'numeric',
                            'groupSeparator' => '.',
                            'radixPoint' => ',',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'unmaskAsNumber' => true,
                            'autoUnmask' => true,
                            'removeMaskOnSubmit' => true,
                            'suffix' => ' ₺'
                        ])
                        ->required(),
                    Input::make('portfolio.sale_price')
                        ->mask([
                            'alias' => 'numeric',
                            'groupSeparator' => '.',
                            'radixPoint' => ',',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'unmaskAsNumber' => true,
                            'autoUnmask' => true,
                            'removeMaskOnSubmit' => true,
                            'suffix' => ' ₺'
                        ])
                        ->title(__('Sale Price')),

                    DateTimer::make('portfolio.contract_date')
                        ->title(__('Contract Date'))
                        ->value($this->portfolio?->contract_date ? $this->portfolio->contract_date : date('Y-m-d'))
                        ->format('Y-m-d'),
                ]),
                Group::make([
                    Input::make('portfolio.latitude')
                        ->title(__('Latitude'))->readonly()->id('form-lat'),
                    Input::make('portfolio.longitude')
                        ->title(__('Longitude'))->readonly()->id('form-long'),
                    ModalToggle::make(__('Open Map'))
                        ->modal('mapModal')
                        ->icon('geo-alt')
                        ->class('btn btn-outline-success btn-block')
                        ->style('max-width: 356px !important; margin-top: 5px; color: #a9a6a6;')
                        ->id('open-map-button')
                        ->title('Haritadan Seç')
                ]),
            ]),
            ProvinceStateListener::class,

            Layout::view('partials/PortfolioPhoto'),

            Layout::rows([
                Upload::make('portfolio.images')
                    ->groups('photo')
                    ->title(__('Portfolio Images'))
                    ->targetRelativeUrl(),

                SimpleMDE::make('portfolio.description')
                    ->title(__('Description')),
            ]),

            Layout::view('partials/PortfolioGroupVariationScript'),
        ];
    }

    public function save(Request $request)
    {
        $request->validate([
            'portfolio.user_id' => 'required',
            'portfolio.contact_id' => 'required',
            'portfolio.title' => 'required|unique:portfolios,title,' . $this->portfolio->id . ',id',
            'portfolio.portfolio_no' => 'required|unique:portfolios,portfolio_no,' . $this->portfolio->id . ',id',
            'portfolio.portfolio_type' => 'required',
            'portfolio.portfolio_group' => 'required',
            'portfolio.portfolio_variation' => 'required',
            'portfolio.portfolio_resource' => 'required',
            'portfolio.list_price' => 'required',
            'portfolio.minimum_price' => 'required',
            'province' => 'required',
            'state' => 'required',
            'neighborhood' => 'required',
        ], [
            'portfolio.user_id.required' => 'Lütfen danışman seçiniz.',
            'portfolio.contact_id.required' => 'Lütfen kişi seçiniz.',
            'portfolio.title.required' => 'Lütfen başlık giriniz.',
            'portfolio.title.unique' => 'Bu başlık zaten kullanılmış.',
            'portfolio.portfolio_no.required' => 'Lütfen portföy numarası giriniz.',
            'portfolio.portfolio_no.unique' => 'Bu portföy numarası zaten kullanılmış.',
            'portfolio.portfolio_type.required' => 'Lütfen portföy tipi seçiniz.',
            'portfolio.portfolio_group.required' => 'Lütfen portföy grubu seçiniz.',
            'portfolio.portfolio_variation.required' => 'Lütfen portföy varyasyonu seçiniz.',
            'portfolio.portfolio_resource.required' => 'Lütfen portföy kaynağı seçiniz.',
            'portfolio.list_price.required' => 'Lütfen liste fiyatı giriniz.',
            'portfolio.minimum_price.required' => 'Lütfen minimum fiyat giriniz.',
            'province.required' => 'Lütfen il seçiniz.',
            'state.required' => 'Lütfen ilçe seçiniz.',
            'neighborhood.required' => 'Lütfen mahalle seçiniz.',

        ]);
        $portfolio = null;
        if ($this->portfolio?->id) {
            $portfolio = $this->portfolio;
        } else {
            $portfolio = new Portfolio();
        }
        $requestPortfolio = $request->get('portfolio');
        if (isset($requestPortfolio['images'])) {
            $requestPortfolio['images'] = json_encode($requestPortfolio['images']);
            $request->merge(['portfolio' => $requestPortfolio]);
        }

        $portfolio->fill($request->get('portfolio'));
        $portfolio->province_id = $request->get('province');
        $portfolio->state_id = $request->get('state');
        $portfolio->neighborhood = $request->get('neighborhood');
        if ($request->has('portfolio.user_id')) {
            $portfolio->user_id = $request->input('portfolio.user_id');
        } else {
            $portfolio->user_id = auth()->user()->id;
        }
        $portfolio->save();
        if (isset($requestPortfolio['images'])) {
            $requestPortfolio['images'] = json_decode($requestPortfolio['images']);
            $request->merge(['portfolio' => $requestPortfolio]);
            $portfolio->attachment()->syncWithoutDetaching(
                $request->input('portfolio.images', [])
            );
        }

        $portfolioRecordType = RecordType::where('name', 'Portföy')->first();
        $record = new Record();
        $record->user_id = $request->has('portfolio.user_id') ? $request->input('portfolio.user_id') : auth()->user()->id;
        $record->contact_id = $request->input('portfolio.contact_id');
        $record->portfolio_id = $portfolio->id;
        $record->province_id = $request->get('province');
        $record->state_id = $request->get('state');
        $record->neighborhood = $request->get('neighborhood');
        $record->record_type_id = $portfolioRecordType->id;
        $record->sales_price = $portfolio->list_price;
        $record->record_date = date('Y-m-d H:i');
        $record->save();



        return redirect()->route('platform.portfolio.edit', $portfolio);
    }

    public function delete()
    {
        if ($this->portfolio->attachment()->count() > 0) {
            $this->portfolio->attachment->each->delete();
        }
        $this->portfolio->delete();

        return redirect()->route('platform.portfolio');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.portfolios.edit'
        ];
    }
}
