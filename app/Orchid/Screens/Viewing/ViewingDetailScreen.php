<?php

namespace App\Orchid\Screens\Viewing;

use App\Models\Record;
use App\Models\Setting;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class ViewingDetailScreen extends Screen
{
    public $viewing;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record): iterable
    {
        return [
            'viewing' => $record,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Viewing Detail';
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
        return [
            Layout::legend('viewing', [
                Sight::make('contact_id', __('Contact'))->render(function (Record $record) {
                    return $record->contactS->name;
                }),
                Sight::make('user_id', __('Consultant'))->render(function (Record $record) {
                    return $record->presenter()->fullName;
                }),
                Sight::make('portfolio_id', __('Portfolio'))->render(function (Record $record) {
                    return Link::make($record->portfolioS->title)->route('platform.portfolio.detail', $record->portfolio_id)->style('padding: 0 !important;')->id('portfolio-link');
                }),
                Sight::make('contact_resource', __('Contact Resource'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['contact_resources'][$record->contact_resource];
                }),
                Sight::make('record_level', __('Severity Level'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['record_levels'][$record->record_level];
                }),
                Sight::make('result', __('Viewing Result'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['demonstration_results'][$record->record_result];
                }),
                Sight::make('budget', __("Caller's Budget"))->render(function (Record $record) {
                    return number_format($record->price_offer, 0, ',', '.') . " ₺";
                }),
                Sight::make('offer_price', __('Offer Price'))->render(function (Record $record) {
                    return number_format($record->price_offer, 0, ',', '.') . " ₺";
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
