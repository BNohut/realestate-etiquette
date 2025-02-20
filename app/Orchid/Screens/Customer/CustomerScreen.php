<?php

namespace App\Orchid\Screens\Customer;

use App\Models\Record;
use App\Models\RecordType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class CustomerScreen extends Screen
{
    public $customers;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $customerType = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $customers = Record::where('record_type_id', $customerType)->get();
        } elseif (authUserInRole('ofis-yoneticisi')) {
            $customers = Record::join('users', 'records.user_id', '=', 'users.id')
                ->where([['records.record_type_id', $customerType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->get();
        } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
            $customers = Record::where([['record_type_id', $customerType], ['user_id', auth()->user()->id]])->get();
        }
        return [
            'customers' => $customers,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Customer Records';
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
                ->route('platform.customer.edit')
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
            Layout::view('Customer/CustomerList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
