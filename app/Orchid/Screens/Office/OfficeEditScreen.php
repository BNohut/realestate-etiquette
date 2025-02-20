<?php

namespace App\Orchid\Screens\Office;

use App\Models\Office;
use App\Models\State;
use App\Models\User;
use App\Orchid\Layouts\ProvinceStateListener;
use App\View\Components\KgImg;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OfficeEditScreen extends Screen
{
    public $office;
    public $province;
    public $state;
    public $neighborhood;
    public $neighborhoods;
    public $portfolio;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Office $office): iterable
    {
        $authUser = auth()->user();
        $office->load('attachment');

        $neighborhoodOptions = [];
        if (isset($office->province_id) || isset($authUser->province_id)) {
            $neighborhoodsList = explode(", ", State::find(isset($office->state_id) ? $office->state_id : $authUser->state_id)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }

        $office->social_media_accounts = $office->social_media_accounts != null ? json_decode($office->social_media_accounts, true) : null;

        return [
            'office' => $office,
            'province' => isset($office->province_id) ? $office->province_id : $authUser->province_id,
            'state' => isset($office->state_id) ? $office->state_id : $authUser->state_id,
            'neighborhood' => isset($office->neighborhood) ? $office->neighborhood : $authUser->neighborhood,
            'neighborhoods' => $neighborhoodOptions,
            'portfolio' => [
                'street' => $office->street ?? null,
                'building_no' => $office->building_no ?? null,
                'apartment_no' => $office->apartment_no ?? null,
            ]
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return isset($this->office->id) ? __('Edit Office') . ' | ' . $this->office->name : 'Add Office';
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
                ->class('btn btn-danger')
                ->method('remove')
                ->confirm(__('Are you sure you want to delete this office?'))
                ->canSee(isset($this->office->id) && authUserInRole('super-yonetici')),
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
            Layout::columns([
                Layout::split([
                    'left' =>
                    [
                        Layout::rows(
                            isset($this->office->id) ?
                                [
                                    KgImg::make($this->office->attachment()->first()->url)->width(120)->height(120)->title(__('Uploaded Logo')),
                                    Upload::make('office.logo')
                                        ->title(isset($this->office->id) ? __('Change Logo') : __('Logo'))
                                        ->acceptedFiles('image/*')
                                        ->maxFiles(1)
                                        ->groups('logo')
                                        ->disabled($this->office->attachment()->get() != null ? true : false),
                                ] : [Upload::make('office.logo')
                                    ->title(isset($this->office->id) ? __('Change Logo') : __('Logo'))
                                    ->acceptedFiles('image/*')
                                    ->maxFiles(1)
                                    ->groups('logo')],
                        ),
                    ],

                    'right' =>
                    [
                        Layout::rows([
                            Group::make([
                                Relation::make('office.user_id')
                                    ->title(__('Manager'))
                                    ->fromModel(User::class, 'name')
                                    ->applyScope('manager')
                                    ->displayAppend('full')
                                    ->required(),
                                Input::make('office.name')
                                    ->title(__('Office Name'))
                                    ->required(),
                            ]),
                            Group::make([
                                Input::make('office.email')
                                    ->title(__('Email Address'))
                                    ->required(),
                                Input::make('office.phone')
                                    ->title(__('Phone Number'))
                                    ->mask('(999) 999-9999')
                                    ->required(),
                            ]),
                            Input::make('office.website')
                                ->title(__('Website'))->style('max-width: 100% !important'),
                            Matrix::make('office.social_media_accounts')
                                ->title(__('Social Media'))
                                ->columns(['Platform', 'Link'])
                                ->fields([
                                    'Platform' => Select::make()->options([
                                        "Facebook" => "Facebook",
                                        "Instagram" => "Instagram",
                                        "Linked-In" => "Linked-In",
                                        "Twitter" => "Twitter"
                                    ])->empty(__('Select Platform')),
                                    'Link' => Input::make()
                                ]),
                        ]),
                        ProvinceStateListener::class
                    ]
                ])->ratio("30/70")
            ]),
        ];
    }

    public function createOrUpdate(Office $office, Request $request)
    {
        $attachment = null;
        $exists = $office->exists;
        $officeRequest = $request->get('office');
        $officeRequest['province_id'] = $request->get('province');
        $officeRequest['state_id'] = $request->get('state');
        $officeRequest['neighborhood'] = $request->get('neighborhood');
        $officeRequest['street'] = $request->get('portfolio')['street'];
        $officeRequest['building_no'] = $request->get('portfolio')['building_no'];
        $officeRequest['apartment_no'] = $request->get('portfolio')['apartment_no'];
        if (!$request->has('office.logo') && $office->attachment()->get()->isEmpty()) {
            $attachment = generateOfficeLogo($officeRequest['name']);
            $officeRequest['logo'] = $attachment;
        }
        $request->merge(['office' => $officeRequest]);
        $request->validate([
            'office.name' => 'required',
            'office.email' => 'required|email|unique:offices,email,' . $office->id,
            'office.phone' => 'required',
            'office.user_id' => 'required|unique:offices,user_id,' . $office->id,
        ], [
            'office.name.required' => __('Office name is required'),
            'office.email.required' => __('Email is required'),
            'office.email.email' => __('Email is invalid'),
            'office.email.unique' => __('Email is already taken'),
            'office.phone.required' => __('Phone number is required'),
            'office.user_id.required' => __('Manager is required'),
            'office.user_id.unique' => __('Manager already has another office'),
        ]);

        // Update the office data based on the conditions
        if ($attachment) {
            $officeData = collect($request->get('office'))->except(['logo', 'social_media_accounts'])->toArray();
            $officeData['logo'] = $attachment->id;
        } else {
            if ($request->has('office.logo')) {
                $office->attachment->each->delete();
                $officeData = collect($request->get('office'))->except(['logo', 'social_media_accounts'])->toArray();
                $officeData['logo'] = $request->get('office')['logo'][0];
            } else {
                $officeData = collect($request->get('office'))->except(['social_media_accounts'])->toArray();
            }
        }

        // Fill and save the office data
        $office->fill($officeData)->fill(['social_media_accounts' => json_encode($request->get('office')['social_media_accounts'] ?? [])])->save();

        $office->attachment()->syncWithoutDetaching(
            $request->input('office.logo', [])
        );
        $officeAttachment = $office->attachment()->first();
        $officeAttachment->group = null;
        $officeAttachment->save();

        // Set The Manager Office Id Field
        $manager = User::find($office->user_id);
        $manager->office_id = $office->id;
        $manager->office_approved_at = now();
        $manager->save();

        if ($exists) {
            Toast::info(__('Office updated successfully'));
        } else {
            Toast::info(__('Office created successfully'));
        }

        return redirect()->route('platform.office');
    }

    public function remove()
    {
        $this->office->delete();

        Toast::info(__('Office deleted successfully'));

        return redirect()->route('platform.office');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.offices.add',
            'platform.offices.edit'
        ];
    }
}
