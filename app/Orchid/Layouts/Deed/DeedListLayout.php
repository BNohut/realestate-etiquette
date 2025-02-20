<?php

namespace App\Orchid\Layouts\Deed;

use App\Models\Office;
use App\Models\Record;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DeedListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'records';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('office_name', __('Office Name'))
                ->canSee(!authUserInRole(['ofis-yoneticisi']))
                ->render(fn (Record $record) => Label::make()->value($record->userS->officeS->name)->style('color: #fff !important;')->class('mb-0')),
            TD::make('contact_id', __('Contact Name'))
                ->render(function (Record $record) {
                    return Link::make($record->contactS->name)
                        ->route('platform.contact.detail', $record->contact_id)
                        ->style('padding-left: 0 !important;')->id('deed-list-contact-name-link');
                }),
            TD::make('user_id', __('Consultant'))
                ->render(function (Record $record) {
                    return Link::make($record->userS->name)
                        ->route('platform.consultant.detail', $record->user_id)
                        ->style('padding-left: 0 !important;')->id('deed-list-consultant-name-link');
                }),
            TD::make('portfolio_id', __('Portfolio Title'))
                ->render(function (Record $record) {
                    return Link::make($record->portfolioS->title)
                        ->route('platform.portfolio.detail', $record->portfolio_id)
                        ->style('padding-left: 0 !important;')->id('portfolio-link');
                }),
            TD::make('sales_price', __('Total Sale Price'))->render(function (Record $record) {
                return Label::make()->value(number_format($record->sales_price, 0, ',', '.') . ' ₺')->style('color: #fff !important;')->class('mb-0');
            }),
            TD::make('activity_type', __('Activity Type'))->render(fn (Record $record) => Label::make()->value($record->activity_type)->style('color: #fff !important;')->class('mb-0')),
            TD::make('record_date', __('Operation Date'))->render(function (Record $record) {
                return Label::make()->value(changeDateFormat($record->record_date, 1))->style('color: #fff !important;')->class('mb-0');
            }),
            TD::make('approved_at', __('Status'))->width('22%')->render(function (Record $record) {
                if ($record->approved_at) {
                    return Button::make(__('Approved'))
                        ->icon('check')
                        ->disabled()
                        ->style('border: 1px solid #28a745 !important; color: #fff !important; background-color: #28a745 !important;');
                } else {
                    if ($record->deleted_at) {
                        return Button::make(__('Rejected'))
                            ->icon('x')
                            ->disabled()
                            ->style('border: 1px solid #dc3545 !important; color: #fff !important; background-color: #dc3545 !important;');
                    }
                    return Group::make([
                        Button::make(__('Approve'))
                            ->icon('check')
                            ->class('btn btn-success btn-sm')
                            ->method('approve', ['recordId' => $record->id]),
                        Button::make(__('Reject'))
                            ->icon('x')
                            ->class('btn btn-danger btn-sm')
                            ->method('reject', ['recordId' => $record->id]),
                    ]);
                }
            })->align(TD::ALIGN_CENTER),
        ];
    }
}
