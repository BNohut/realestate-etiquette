<?php

namespace App\Orchid\Screens\Record;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Models\Record;

class RecordScreen extends Screen
{
    public $records;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $records = Record::orderBy('record_date', 'desc')->get();
        } elseif (authUserInRole('ofis-yoneticisi')) {
            $records = Record::join('users', 'records.user_id', '=', 'users.id')
                ->where('users.office_id', auth()->user()->office_id)->select('records.*')->orderBy('record_date', 'desc')->get();
        } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
            $records = Record::where('user_id', auth()->user()->id)->orderBy('record_date', 'desc')->get();
        }
        return [
            'records' => $records,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Records');
    }

    public function description(): string
    {
        return "";
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
            Layout::view('Record/RecordList'),
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records',
        ];
    }
}
