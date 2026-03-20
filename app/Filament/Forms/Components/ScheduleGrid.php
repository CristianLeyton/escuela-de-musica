<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Contracts\View\View;
use Closure;

class ScheduleGrid extends Field
{
    protected string $view = 'filament.forms.components.schedule-grid';
    protected bool|Closure|null $isDehydrated = false;

    public $teacher = null;
    public $showInCreate = false;

    public function teacher($teacher): static
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function showInCreate(bool|Closure $show = true): static
    {
        $this->showInCreate = $show;
        return $this;
    }

    public function getData(): array
    {
        return [
            'teacher' => $this->teacher,
            'showInCreate' => $this->showInCreate,
        ];
    }
}
