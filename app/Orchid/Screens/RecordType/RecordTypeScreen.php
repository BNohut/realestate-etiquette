<?php

namespace App\Orchid\Screens\RecordType;

use App\Models\RecordType;
use App\Orchid\Layouts\RecordType\RecordTypeListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class RecordTypeScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'recordTypes' => RecordType::filters()->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Record Types');
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
                ->icon('plus')
                ->class('commandbar-add-button btn')
                ->route('platform.recordtype.edit')
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
            RecordTypeListLayout::class,
        ];
    }

    public function remove(Request $request)
    {
        RecordType::find($request->recordType)->delete();

        Toast::info(__('You have successfully deleted the record type'));

        return redirect()->route('platform.recordtypes');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.recordtypes',
        ];
    }
}
