<?php

namespace App\Orchid\Screens\Viewing;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ViewingScreen extends Screen
{
    public $viewings;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $viewingTypeId = RecordType::where('name', 'Yer Gösterme')->first()->id;
        if (request()->portfolio) {
            $viewings = Record::where([['record_type_id', $viewingTypeId], ['portfolio_id', request()->portfolio]])->get();
        } else {
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $viewings = Record::where('record_type_id', $viewingTypeId)->get();
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $viewings = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $viewingTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $viewings = Record::where([['record_type_id', $viewingTypeId], ['user_id', auth()->user()->id]])->get();
            }
        }

        return [
            'viewings' => $viewings,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Viewing');
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
                ->route('platform.viewing.edit')
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
            Layout::view('Viewing/ViewingList'),
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
