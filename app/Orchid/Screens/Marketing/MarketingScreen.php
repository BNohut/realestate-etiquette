<?php

namespace App\Orchid\Screens\Marketing;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class MarketingScreen extends Screen
{
    public $marketings;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $marketingType = RecordType::where('name', 'Pazarlama')->first()->id;
        if (request()->portfolio) {
            $marketings = Record::where([['record_type_id', $marketingType], ['portfolio_id', request()->portfolio]])->get();
        } else {
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $marketings = Record::where('record_type_id', $marketingType)->get();
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $marketings = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $marketingType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $marketings = Record::where([['record_type_id', $marketingType], ['user_id', auth()->user()->id]])->get();
            }
        }

        return [
            'marketings' => $marketings
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Marketing Records';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Add')
                ->class('commandbar-add-button btn')
                ->icon('plus')
                ->route('platform.marketing.edit')
                ->canSee(hasUserPermission('platform.records.add')),
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
            Layout::view('Marketing/MarketingList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
