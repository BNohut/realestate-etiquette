<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Contact;
use App\Models\Follower;
use App\Models\Record;
use App\Models\User;
use App\Orchid\Layouts\Contact\ContactFormLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PlatformScreen extends Screen
{
    public $records;
    public $counts;
    public $champions;
    public $championsFilter;
    public $countsFilter;
    public $followingsIds;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Some Variables
        $authUser = auth()->user();
        // If request has consultant query
        if (request()->consultant) {
            $filteredUser = request()->consultant;
        }
        // Handle Champions by using request query
        // Get request query
        if (request()->champions) {
            if (request()->champions == 'today') {
                $championsFilter = 'champsToday';
            } elseif (request()->champions == 'week') {
                $championsFilter = 'champsWeek';
            }
        } else {
            $championsFilter = 'all';
        }
        // Create A Query of Users depending on records count 
        $query = Record::leftJoin('users', 'users.id', 'records.user_id')
            ->select('user_id', DB::raw('COUNT(*) as record_count'))
            ->groupBy('user_id')
            ->orderByDesc('record_count');

        // Filter Champions by request query
        if ($championsFilter === 'champsToday') {
            // Get records of today (last 24 hours)
            $query->where('records.created_at', '>', now()->subDay());
        } elseif ($championsFilter === 'champsWeek') {
            // Get records of this week(Monday to Sunday)
            $query->whereBetween('records.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }
        // Get Champions Ids into an array
        $championsIds = $query->limit(5)->pluck('user_id')->toArray();
        // Get Champions User Details
        if (count($championsIds) > 0) {
            $champions = User::whereIn('id', $championsIds)
                ->orderBy(DB::raw("FIELD(id, " . implode(',', $championsIds) . ")"))
                ->get(['id', 'name', 'last_name', 'visibility']);
        }
        // Handle Counts by using request query
        // Get request query
        if (request()->counts) {
            if (request()->counts == 'today') {
                $countsFilter = 'countsToday';
            } elseif (request()->counts == 'week') {
                $countsFilter = 'countsWeek';
            } elseif (request()->counts == 'month') {
                $countsFilter = 'countsMonth';
            }
        } else {
            $countsFilter = 'all';
        }

        // Handle feed records
        // If user is super-yonetici or yonetici, get all records
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $records = Record::orderBy('created_at', 'desc')->get();
            $counts = countsOfSystemRecords($filteredUser ?? null, $countsFilter);
        } elseif (authUserInRole('ofis-yoneticisi')) {
            // If user is ofis-yoneticisi, get records of his/her office
            $records = Record::join('users', 'users.id', 'records.user_id')
                ->where('users.office_id', $authUser->office_id)->select('records.*')->orderBy('created_at', 'desc')->get();
            $counts = countsOfOfficeRecords($filteredUser ?? null, $countsFilter);
        } elseif (authUserInRole('ofis-danismani')) {
            $officeConsultantIds = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-danismani')
                ->where('office_id', auth()->user()->office_id)
                ->select('users.id')
                ->get()
                ->pluck('id')
                ->toArray();
            $followingsIds = Follower::where('from', auth()->user()->id)
                ->whereNotNull('approved')
                ->groupBy('followers.from', 'followers.to')
                ->pluck('to')
                ->toArray();
            $records = Record::whereIn('user_id', array_unique(array_merge($followingsIds, $officeConsultantIds)))
                ->orderBy('created_at', 'desc')
                ->get();
            $counts = countsOfUserRecords($filteredUser ?? $authUser->id, $countsFilter);
        } else {
            $followingsIds = Follower::where('from', $authUser->id)->whereNotNull('approved')->groupBy('followers.from', 'followers.to')->pluck('to')->toArray();
            $records = Record::whereIn('user_id', $followingsIds)->orWhere('user_id', $authUser->id)->orderBy('created_at', 'desc')->get();
            $counts = countsOfUserRecords($filteredUser ?? $authUser->id, $countsFilter);
        }

        return [
            'records' => $records,
            'counts' => $counts,
            'champions' => $champions ?? [],
            'championsFilter' => $championsFilter,
            'countsFilter' => $countsFilter,
            'followingsIds' => $followingsIds ?? [],
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
        return __('Home');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return '';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            DropDown::make()
                ->icon('plus')
                ->id('fab-button')
                ->list([
                    ModalToggle::make(__('Add New Contact'))
                        ->icon('person-add')
                        ->id('addContactModal')
                        ->modal('addContactModal')
                        ->method('addContact'),
                    Link::make(__('Call'))->icon('headset')->route('platform.call.edit'),
                    Link::make(__('FSBO'))->icon('diamond')->route('platform.fsbo.edit'),
                    Link::make(__('Viewing'))->icon('flag')->route('platform.viewing.edit'),
                    Link::make(__('Customer'))->icon('list')->route('platform.customer.edit'),
                    Link::make(__('Marketing'))->icon('magic')->route('platform.marketing.edit'),
                    Link::make(__('Sale Closing'))->icon('rocket')->route('platform.sale.edit'),
                    Link::make(__('Deed Sale-Rent Process'))->icon('file-earmark-medical')->route('platform.deed.edit'),
                ])->canSee(!authUserInRole('ofis-asistani')),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            // Layout::rows([
            //     Group::make([
            //         // Link::make(),
            //         Input::make('search')->placeholder(__('What are you looking for?'))->class('form-control search-input mx-auto'),
            //         // Link::make()->icon('magnifier')->class('search-icon')
            //     ])->alignCenter(),
            // ]),
            Layout::modal('addContactModal', [
                Layout::view('partials/ProfilePhoto', ['edit' => false, 'contact' => null, 'contactModal' => true, 'firstCall' => true]),
                ContactFormLayout::class,
            ])->applyButton(__('Save'))->withoutCloseButton()->title(__('Add New Contact')),

            // FeedListLayout::class,
            Layout::view('Feed'),
        ];
    }

    public function addContact(Contact $contact, Request $request)
    {
        $request->validate([
            'contact.name' => 'required',
            'contact.phone' => 'required',
            'contact.email' => 'required|email|unique:contacts,email',
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
        Toast::info(__('Contact added successfully'));
    }

    public function like(Request $request)
    {
        $record = Record::find($request->recordId);
        if ($record->likes) {
            $decodedLikes = json_decode($record->likes);
            if (!in_array(auth()->user()->id, $decodedLikes)) {
                $decodedLikes[] = auth()->user()->id;
                $record->likes = json_encode($decodedLikes);
                $record->save();
            }
        } else {
            $record->likes = json_encode([auth()->user()->id]);
            $record->save();
        }
    }

    public function unlike(Request $request)
    {
        $record = Record::find($request->recordId);
        $decodedLikes = json_decode($record->likes, true);
        if (($key = array_search(auth()->user()->id, $decodedLikes)) !== false) {
            unset($decodedLikes[$key]);
            if (count($decodedLikes) > 0) {
                $record->likes = json_encode($decodedLikes);
            } else {
                $record->likes = null;
            }
            $record->save();
        }
    }
}
