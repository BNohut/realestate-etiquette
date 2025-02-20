<?php

namespace App\Orchid\Screens\RecordType;

use App\Models\RecordType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RecordTypeEditScreen extends Screen
{
    public $recordType;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'recordType' => request()->recordtype ? RecordType::find(request()->recordtype) : null,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->recordType ?
            __('Edit Record Type') . " | " . $this->recordType->name :
            __('Add Record Type');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('save')
                ->class('commandbar-save-button btn')
                ->method('createOrUpdate'),

            Button::make(__('Remove'))
                ->icon('trash')
                ->method('remove')
                ->class('btn btn-danger')
                ->confirm(__('Do you want to delete this record type?'))
                ->canSee($this->recordType != null),
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
            Layout::rows([
                Input::make('recordType.name')->title(__('Record Type Name')),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        if ($this->recordType != null) {
            $recordType = $this->recordType;
        } else {
            $recordType = new RecordType();
        }

        $rules = array(
            'name' => [Rule::unique($recordType::class, 'name')->ignore($recordType)],
        );
        $messages = array(
            'unique' => __('The record type with this name already exists')
        );

        Validator::make($request->get('recordType'), $rules, $messages)->validate();

        $recordType->fill($request->get('recordType'))->save();

        Toast::info($this->recordType != null ? __('You have successfully updated a record type') : __('You have successfully created a record type'));

        return redirect()->route('platform.recordtypes');
    }

    public function remove(Request $request)
    {
        if ($this->recordType != null) {
            $recordType = $this->recordType;
        }
        $recordType->delete();

        Toast::info(__('You have successfully deleted the record type'));

        return redirect()->route('platform.recordtypes');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.recordtypes.edit',
        ];
    }
}
