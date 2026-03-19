<?php

namespace App\Filament\Widgets;



use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Pages\WeeklySchedule;


use Filament\Actions\CreateAction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class Panel extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = '2';
    protected static bool $isLazy = false;

    protected function getColumns(): int | array
    {
        return [
            'default' => 1, // mobile
            'sm' => 2,      // tablets
            'md' => 2,      // pantallas intermedias
            'lg' => 4,      // desktop
        ];
    }


    protected function getStats(): array
    {
        return [
            Stat::make('Listado de profesores', 'Profesores')
                ->icon('heroicon-o-user-circle')
                ->url(TeacherResource::getUrl('index'))
                ->description('Ir a listado de profesores')
                ->descriptionIcon('heroicon-m-arrow-up-right')
                ->extraAttributes(['class' => 'group [&_.fi-wi-stats-overview-stat-value]:text-2xl [&_.fi-wi-stats-overview-stat-value]:group-hover:text-primary-600 [&_.fi-wi-stats-overview-stat-value]:transition
                [&_.fi-icon:nth-child(2)]:group-hover:translate-x-0.5 [&_.fi-icon:nth-child(2)]:group-hover:-translate-y-0.5 [&_.fi-icon]:transition']),

            Stat::make('Listado de alumnos', 'Alumnos')
                ->icon('heroicon-o-academic-cap')
                ->url(StudentResource::getUrl('index'))
                ->description('Ir a listado de alumnos')
                ->descriptionIcon('heroicon-m-arrow-up-right')
                ->extraAttributes(['class' => 'group [&_.fi-wi-stats-overview-stat-value]:text-2xl [&_.fi-wi-stats-overview-stat-value]:group-hover:text-primary-600 [&_.fi-wi-stats-overview-stat-value]:transition
                [&_.fi-icon:nth-child(2)]:group-hover:translate-x-0.5 [&_.fi-icon:nth-child(2)]:group-hover:-translate-y-0.5 [&_.fi-icon]:transition']),

            Stat::make('Listado de clases', 'Clases')
                ->icon('heroicon-o-academic-cap')
                ->url(WeeklySchedule::getUrl())
                ->description('Ir a listado de clases')
                ->descriptionIcon('heroicon-m-arrow-up-right')
                ->extraAttributes(['class' => 'group [&_.fi-wi-stats-overview-stat-value]:text-2xl [&_.fi-wi-stats-overview-stat-value]:group-hover:text-primary-600 [&_.fi-wi-stats-overview-stat-value]:transition
                [&_.fi-icon:nth-child(2)]:group-hover:translate-x-0.5 [&_.fi-icon:nth-child(2)]:group-hover:-translate-y-0.5 [&_.fi-icon]:transition']),

            Stat::make('Administrar usuarios', 'Usuarios')
                ->icon('heroicon-o-user-group')
                ->url(UserResource::getUrl())
                ->description('Crear o editar usuarios')
                ->descriptionIcon('heroicon-m-arrow-up-right')
                ->extraAttributes(['class' => 'group [&_.fi-wi-stats-overview-stat-value]:text-2xl [&_.fi-wi-stats-overview-stat-value]:group-hover:text-primary-600 [&_.fi-wi-stats-overview-stat-value]:transition
                [&_.fi-icon:nth-child(2)]:group-hover:translate-x-0.5 [&_.fi-icon:nth-child(2)]:group-hover:-translate-y-0.5 [&_.fi-icon]:transition'])
                ->visible(fn(): bool => Auth::user()->hasRole('admin') ?? false),
        ];
    }
}
