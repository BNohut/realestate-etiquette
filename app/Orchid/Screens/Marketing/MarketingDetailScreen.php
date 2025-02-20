<?php

namespace App\Orchid\Screens\Marketing;

use App\Models\Record;
use App\Models\Setting;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class MarketingDetailScreen extends Screen
{
    public $marketing;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record): iterable
    {
        return [
            'marketing' => $record,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Maketing Record Detail';
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
            Layout::legend('marketing', [
                Sight::make('user_id', __('Consultant'))->render(function (Record $record) {
                    return $record->presenter()->fullName;
                }),
                Sight::make('portfolio_id', __('Portfolio'))->render(function (Record $record) {
                    return Link::make($record->portfolioS->title)->route('platform.portfolio.detail', $record->portfolio_id)->style('padding: 0 !important;')->id('portfolio-link');
                }),
                Sight::make('activity_type', __('Activity Type'))->render(function (Record $record) use ($jsonData) {
                    return $jsonData['activity_types'][$record->activity_type];
                }),
                Sight::make('link', __('Share Link'))->render(function (Record $record) {
                    return Link::make($record->link)->href($record->link)->style('padding: 0 !important;')->id('marketing-legend-link');
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
