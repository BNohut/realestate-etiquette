<?php

namespace App\Orchid\Screens\FSBO;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class FSBOScreen extends Screen
{
    public $fsbos;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $fsboTypeId = RecordType::where('name', 'F.S.B.O.')->first()->id;
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $fsbos = Record::where('record_type_id', $fsboTypeId)->get();
        } elseif (authUserInRole('ofis-yoneticisi')) {
            $fsbos = Record::join('users', 'records.user_id', '=', 'users.id')
                ->where([['records.record_type_id', $fsboTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
        } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
            $fsbos = Record::where([['record_type_id', $fsboTypeId], ['user_id', auth()->user()->id]])->get();
        }
        return [
            'fsbos' => $fsbos,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return "FSBO | For Sale By Owner";
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
                ->route('platform.fsbo.edit')
                ->icon('plus')
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
            Layout::view('FSBO/FSBOList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
