<?php

namespace App\Orchid\Screens\Sale;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SaleScreen extends Screen
{
    public $sales;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $saleTypeId = RecordType::where('name', 'Satış Kapama')->first()->id;
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $sales = Record::where('record_type_id', $saleTypeId)->get();
        } elseif (authUserInRole('ofis-yoneticisi')) {
            $sales = Record::join('users', 'records.user_id', '=', 'users.id')
                ->where([['records.record_type_id', $saleTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
        } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
            $sales = Record::where([['record_type_id', $saleTypeId], ['user_id', auth()->user()->id]])->get();
        }
        return [
            'sales' => $sales,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Sale Closing Records';
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
                ->route('platform.sale.edit')
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
            Layout::view('Sale/SaleList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
