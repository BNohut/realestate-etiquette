<?php

namespace App\View\Components;

use Orchid\Screen\Field;
use Orchid\Screen\Contracts\Fieldable;

class KgImg extends Field implements Fieldable
{
    public function src(string $src): self
    {
        $this->set('src', $src);
        return $this;
    }

    public static function make(?string $src = null): self
    {
        $instance = new static();

        $instance->set('name', 'kg-img');
        if ($src !== null) {
            $instance->set('src', $src);
        }

        return $instance;
    }

    public function render()
    {
        return view('components.kg-img', [
            'src' => $this->get('src'),
            'title' => $this->get(('title')),
            'id' => $this->get(('id')),
            'alt' => $this->get(('alt')),
            'class' => $this->get(('class')),
            'attr' => $this->get(('attr')),
            'width' => $this->get(('width')),
            'height' => $this->get(('height')),
        ]);
    }
}
