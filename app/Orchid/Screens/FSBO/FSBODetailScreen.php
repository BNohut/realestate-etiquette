<?php

namespace App\Orchid\Screens\FSBO;

use App\Models\Record;
use App\Models\Setting;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class FSBODetailScreen extends Screen
{
    public $record;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record): iterable
    {
        return [
            'record' => $record,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('FSBO Detail');
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

        if ($this->record->exists) {
            $variationList = $jsonData['portfolio_groups'][$this->record->portfolio_group];
        }
        return [
            Layout::legend('record', [
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
                Sight::make('contact_resource', __('Contact Resource'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['contact_resources'][$record->contact_resource];
                }),
                Sight::make('record_level', __('Severity Level'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['record_levels'][$record->record_level];
                }),
                Sight::make('result', __('FSBO Result'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['fsbo_results'][$record->record_result];
                }),
                Sight::make('price_offer', __('Offer Price'))->render(function (Record $record) {
                    return number_format($record->price_offer, 0, ',', '.') . " ₺";
                }),
                Sight::make('sales_price', __('Real Price'))->render(function (Record $record) {
                    return number_format($record->sales_price, 0, ',', '.') . " ₺";
                }),
                Sight::make('created_at', __('Date'))->render(function (Record $record) {
                    return changeDateFormat($record->created_at);
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
