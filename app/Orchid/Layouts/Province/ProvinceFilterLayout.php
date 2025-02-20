<?php

namespace App\Orchid\Layouts\Province;

use App\Orchid\Filters\ProvinceFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ProvinceFilterLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            ProvinceFilter::class,
        ];
    }
}
