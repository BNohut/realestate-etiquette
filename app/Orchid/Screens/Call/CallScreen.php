<?php

namespace App\Orchid\Screens\Call;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class CallScreen extends Screen
{
    public $calls;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $callTypeId = RecordType::where('name', 'Çağrı')->first()->id;
        if (request()->portfolio) {
            $calls = Record::where([['record_type_id', $callTypeId], ['portfolio_id', request()->portfolio]])->get();
        } else {
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $calls = Record::where('record_type_id', $callTypeId)->get();
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $calls = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $callTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $calls = Record::where([['record_type_id', $callTypeId], ['user_id', auth()->user()->id]])->get();
            }
        }

        return [
            'calls' => $calls
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Call Records');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->class('commandbar-add-button btn')
                ->icon('plus')
                ->route('platform.call.edit')
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
            Layout::view('Call/CallList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records'
        ];
    }
}
