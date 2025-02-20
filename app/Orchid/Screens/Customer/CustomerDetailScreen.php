<?php

namespace App\Orchid\Screens\Customer;

use App\Models\Record;
use App\Models\Setting;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class CustomerDetailScreen extends Screen
{
    public $customer;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record): iterable
    {
        return [
            'customer' => $record,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Customer Record Detail';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
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

        if (isset($this->customer->id)) {
            $variationList = $jsonData['portfolio_groups'][$this->customer->portfolio_group];
        }
        return [
            Layout::legend('customer', [
                Sight::make('contact_id', __('Contact'))->render(function (Record $record) {
                    return $record->contactS->name;
                }),
                Sight::make('user_id', __('Consultant'))->render(function (Record $record) {
                    return $record->userS->name . " " . $record->userS->last_name;
                }),
                Sight::make('province_id', __('Location'))->render(function (Record $record) {
                    return $record->presenter()->fullAddress;
                }),
                Sight::make('portfolio_type', __('Portfolio Type'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['portfolio_types'][$record->portfolio_type];
                }),
                Sight::make('portfolio_group', __('Portfolio Group'))->render(function (Record $record) use ($newList) {
                    return $newList[$record->portfolio_group];
                }),
                Sight::make('portfolio_type', __('Portfolio Variation'))->render(function (Record $record) use ($variationList) {
                    return $variationList[$record->portfolio_variation];
                }),

                Sight::make('property_features', __('Property Features')),

                Sight::make('budget', __('Customer Max Budget'))->render(function (Record $record) {
                    return number_format($record->budget, 0, ',', '.') . " ₺";
                }),

                Sight::make('contact_resource', __('Contact Resource'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['contact_resources'][$record->contact_resource];
                }),

                Sight::make('record_level', __('Severity Level'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['record_levels'][$record->record_level];
                }),

                Sight::make('record_date', __('Date'))->render(function (Record $record) {
                    return changeDateFormat($record->record_date, 1);
                }),
                Sight::make('notes', __('Note'))->render(function (Record $record) {
                    return $record->notes == null ? "-" : $record->notes;
                }),
            ]),
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.detail',
        ];
    }
}
