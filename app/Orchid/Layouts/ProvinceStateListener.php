<?php

namespace App\Orchid\Layouts;

use App\Models\Province;
use App\Models\State;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class ProvinceStateListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = ['province', 'state'];

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    protected function layouts(): iterable
    {
        return [
            Layout::rows([
                Group::make([
                    Select::make('province')
                        ->fromModel(Province::class, 'name')
                        ->empty(__('Select'))
                        ->title('Province')
                        ->required(),

                    Select::make('state')
                        ->title('State')
                        ->fromQuery(State::where('province_id', $this->query->get('province', [])), 'name')
                        ->empty(__('Select'))
                        ->disabled(empty($this->query->get('province')))
                        ->required()
                        ->value($this->query->get('state')),

                    Select::make('neighborhood')
                        ->title('Neighborhood')
                        ->options($this->query->get('neighborhoods', []))
                        ->empty(__('Select'))
                        ->disabled(empty($this->query->get('state')))
                        ->required(),
                ]),
                Group::make([
                    Input::make('portfolio.street')
                        ->title(__('Street')),
                    Input::make('portfolio.building_no')
                        ->title('Dış Kapı No'),
                    Input::make('portfolio.apartment_no')
                        ->title('İç Kapı No'),
                ])
            ]),
        ];
    }

    /**
     * Update state
     *
     * @param \Orchid\Screen\Repository $repository
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Orchid\Screen\Repository
     */
    public function handle(Repository $repository, Request $request): Repository
    {
        $province = $request->get('province');
        $state = $request->get('state');
        $neighborhoodsList = [];
        $neighborhoodOptions = [];
        if ($state) {
            $neighborhoodsList = explode(", ", State::find($state)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }

        return $repository
            ->set('province', $province)
            ->set('state', $state)
            ->set('neighborhoods', $neighborhoodOptions);
    }
}
