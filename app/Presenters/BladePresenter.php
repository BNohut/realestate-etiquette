<?php

namespace App\Presenters;

use Orchid\Support\Presenter;

class BladePresenter extends Presenter
{
    public function fullAddress(): string
    {
        return $this->entity->provinceS->name . ' / ' . $this->entity->stateS->name . ' / ' . $this->entity->neighborhood;
    }

    public function fullName(): string
    {
        return $this->entity->userS->name . " " . $this->entity->userS->last_name;
    }
}
