<?php

namespace App\Orchid\Screens\Sale;

use App\Models\Record;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class SaleDetailScreen extends Screen
{
    public $sale;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record): iterable
    {
        return [
            'sale' => $record,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Sale Closing Detail';
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
        return [
            Layout::legend('sale', [
                Sight::make('contact_id', __('Contact'))->render(function (Record $record) {
                    return $record->contactS->name;
                }),
                Sight::make('user_id', __('Consultant'))->render(function (Record $record) {
                    return $record->presenter()->fullName;
                }),
                Sight::make('portfolio_id', __('Portfolio'))->render(function (Record $record) {
                    return Link::make($record->portfolioS->title)->route('platform.portfolio.detail', $record->portfolio_id)->style('padding: 0 !important;')->id('portfolio-link');
                }),
                Sight::make('sales_price', __('Total Sale Price'))->render(function (Record $record) {
                    return number_format($record->sales_price, 0, ',', '.') . " ₺";
                }),
                Sight::make('prepayment', __('Taken Deposit'))->render(function (Record $record) {
                    return number_format($record->prepayment, 0, ',', '.') . " ₺";
                }),
                Sight::make('record_date', __('Operation Date'))->render(function (Record $record) {
                    return changeDateFormat($record->deed_date, 1);
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
