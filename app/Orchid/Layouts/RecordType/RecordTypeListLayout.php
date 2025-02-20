<?php

namespace App\Orchid\Layouts\RecordType;

use App\Models\RecordType;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class RecordTypeListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'recordTypes';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', __('Record Type Name'))->sort()->filter(TD::FILTER_TEXT)
                ->render(function (RecordType $recordType) {
                    return Link::make($recordType->name)->route('platform.recordtype.edit', $recordType->id)->id('recordtype-list-name-link');
                }),

            TD::make(__('Actions'))->align(TD::ALIGN_CENTER)->width('12%')
                ->render(fn (RecordType $recordType) => DropDown::make()
                    ->icon('three-dots-vertical')
                    ->id('recordtype-list-dropdown')
                    ->style('color: #fff !important;')
                    ->list([
                        Link::make(__('Edit'))->icon('pencil')
                            ->id('recordtype-list-dropdown-edit')
                            ->route('platform.recordtype.edit', $recordType->id),
                        Button::make(__('Delete'))
                            ->icon('trash')
                            ->id('recordtype-list-dropdown-delete')
                            ->confirm(__('Do you want to delete this record type?'))
                            ->method('remove', ['recordType' => $recordType->id])
                    ])),
        ];
    }
}
