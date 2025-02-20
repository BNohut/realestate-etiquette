<?php

namespace App\Orchid\Screens\Marketing;

use App\Models\Portfolio;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\Setting;
use App\Models\User;
use App\Orchid\Layouts\ConsultantContactFormLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class MarketingEditScreen extends Screen
{
    public $marketing;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Record $record, Request $request): iterable
    {
        return [
            'marketing' => $record
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return isset($this->marketing->id) ? 'Edit Marketing Record' : 'Add New Marketing Record';
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
                ->class('commandbar-save-button btn')
                ->icon('save')
                ->method('save'),
            Button::make(__('Delete'))
                ->icon('trash')
                ->method('delete')
                ->class('btn btn-danger')
                ->confirm(__('Are you sure you want to delete this marketing record?'))
                ->canSee(isset($this->marketing->id) && canUserDelete()),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $jsonData = json_decode(Setting::first()->config, true);
        $canSelect = authUserCanSelectConsultantForRecord();
        return [
            // Call Scripts for listening selectbox changes
            Layout::view('partials/ConsultantContactScript'),
            Layout::split([

                $canSelect ?
                    new ConsultantContactFormLayout('marketing', null) :
                    Layout::rows([
                        Relation::make('marketing.portfolio_id')
                            ->fromModel(Portfolio::class, 'title')
                            ->applyScope('officeOrConsultant')
                            ->title(__('Portfolio'))
                            ->empty(__('Select'))
                            ->required(),
                    ]),

                Layout::rows([
                    Group::make([
                        Select::make('marketing.activity_type')
                            ->options($jsonData['activity_types'])
                            ->title(__('Activity Type'))
                            ->empty(__('Select'))
                            ->required(),
                        DateTimer::make('marketing.record_date')
                            ->title(__('Date'))
                            ->value($this->marketing?->record_date ? $this->marketing->record_date : date('Y-m-d'))
                            ->format('Y-m-d'),
                    ]),

                    Input::make('marketing.link')
                        ->title(__('Share Link'))
                        ->placeholder('https://')
                        ->style('max-width: 100% !important;'),

                    TextArea::make('marketing.notes')
                        ->title(__('Activity Note'))
                        ->maxlength(200)
                        ->style('max-width: 100% !important;'),
                ])
            ])->ratio('50/50')
        ];
    }

    public function save(Request $request)
    {
        if (isset($this->marketing->id)) {
            $exists = true;
            $record = Record::find($this->marketing->id);
        } else {
            $exists = false;
            $record = new Record();
        }
        $marketingId = RecordType::where('name', 'Pazarlama')->first()->id;
        $record->record_type_id = $marketingId;
        if ($request->has('marketing.user_id')) {
            $record->user_id = $request->input('marketing.user_id');
        } else {
            $record->user_id = auth()->user()->id;
        }
        $record->fill($request->get('marketing'))->save();

        Toast::info($exists ? __('You have successfully updated the marketing record.') : __('You have successfully created a new marketing record.'));

        return redirect()->route('platform.marketing.edit', $record->id);
    }

    public function delete()
    {
        $this->marketing->delete();

        Toast::info(__('You have successfully deleted the marketing record.'));

        return redirect()->route('platform.marketing');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.records.edit',
        ];
    }
}
