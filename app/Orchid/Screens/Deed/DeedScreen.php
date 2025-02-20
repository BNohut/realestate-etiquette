<?php

namespace App\Orchid\Screens\Deed;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class DeedScreen extends Screen
{
    public $deeds;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $deedTypeId = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $deeds = Record::where('record_type_id', $deedTypeId)->get();
        } elseif (authUserInRole('ofis-yoneticisi')) {
            $deeds = Record::join('users', 'records.user_id', '=', 'users.id')
                ->whereNotNull('records.approved_at')
                ->where([['records.record_type_id', $deedTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
        } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
            $deeds = Record::whereNotNull('approved_at')->where([['record_type_id', $deedTypeId], ['user_id', auth()->user()->id]])->get();
        }
        return [
            'deeds' => $deeds
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Deed Sale-Rent Processes';
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
                ->route('platform.deed.edit')
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
            Layout::view('Deed/DeedList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
