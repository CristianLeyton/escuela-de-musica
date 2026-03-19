<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class WeeklyScheduleView extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static ?string $title = 'Horarios Semanales';

    protected static ?string $navigationLabel = 'Horarios Semanales';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = -1;

    public function getTitle(): string
    {
        return 'Horarios Semanales';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Actualizar')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->dispatch('refreshSchedule')),

            Action::make('manage')
                ->label('Gestionar Horarios')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => ScheduleResource::getUrl('index')),
        ];
    }

    protected function getViewData(): array
    {
        return [];
    }

    public function getView(): string
    {
        return 'filament.resources.schedules.pages.weekly-schedule-view';
    }
}
