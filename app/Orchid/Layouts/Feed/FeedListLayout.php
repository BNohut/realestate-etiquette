<?php

namespace App\Orchid\Layouts\Feed;

use App\Models\Record;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class FeedListLayout extends Table
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
            TD::make('user_id', __('User'))
                ->render(function (Record $model) {
                    return $model->userS->name;
                }),
            TD::make('feed_message', __('Feed Message')),
        ];
    }
}
