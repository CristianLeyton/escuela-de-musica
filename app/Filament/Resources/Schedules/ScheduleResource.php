<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\ManageSchedules;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Instrument;
use App\Models\Schedule;
use App\Models\Teacher;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    protected static ?string $navigationLabel = 'Horarios';

    protected static ?string $modelLabel = 'Horario';

    protected static ?string $pluralModelLabel = 'Horarios';

    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('day_of_week')
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                        'Domingo' => 'Domingo',
                    ])
                    ->required()
                    ->label('Día de la Semana'),
                TimePicker::make('start_time')
                    ->required()
                    ->label('Hora de Inicio'),
                TimePicker::make('end_time')
                    ->required()
                    ->label('Hora de Fin'),
                Select::make('teacher_id')
                    ->relationship('teacher', 'user.name')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return Teacher::whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%");
                        })->with('user')
                            ->limit(50)
                            ->get()
                            ->pluck('user.name', 'id');
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $teacher = Teacher::find($value);
                        return $teacher ? $teacher->user->name . ' ' . $teacher->user->lastname : $value;
                    })
                    ->required()
                    ->label('Profesor'),
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->required()
                    ->label('Sede'),
                Select::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->required()
                    ->label('Aula'),
                Select::make('instrument_id')
                    ->relationship('instrument', 'name')
                    ->searchable()
                    ->required()
                    ->label('Instrumento'),
                Select::make('status')
                    ->options([
                        'available' => 'Disponible',
                        'occupied' => 'Ocupado',
                        'cancelled' => 'Cancelado',
                    ])
                    ->required()
                    ->default('available')
                    ->label('Estado'),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->label('Notas'),
                Toggle::make('is_active')
                    ->required()
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('day_of_week')
                    ->label('Día'),
                TextEntry::make('start_time')
                    ->time()
                    ->label('Inicio'),
                TextEntry::make('end_time')
                    ->time()
                    ->label('Fin'),
                TextEntry::make('teacher.user.name')
                    ->label('Profesor'),
                TextEntry::make('branch.name')
                    ->label('Sede'),
                TextEntry::make('classroom.name')
                    ->label('Aula'),
                TextEntry::make('instrument.name')
                    ->label('Instrumento'),
                TextEntry::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'warning',
                        'cancelled' => 'danger',
                    }),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label('Notas'),
                IconEntry::make('is_active')
                    ->boolean()
                    ->label('Activo'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(Schedule $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week')
                    ->searchable()
                    ->label('Día')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->time()
                    ->sortable()
                    ->label('Inicio'),
                TextColumn::make('end_time')
                    ->time()
                    ->sortable()
                    ->label('Fin'),
                TextColumn::make('teacher.user.name')
                    ->searchable()
                    ->label('Profesor'),
                TextColumn::make('branch.name')
                    ->searchable()
                    ->label('Sede'),
                TextColumn::make('classroom.name')
                    ->searchable()
                    ->label('Aula'),
                TextColumn::make('instrument.name')
                    ->searchable()
                    ->label('Instrumento'),
                TextColumn::make('status')
                    ->searchable()
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'warning',
                        'cancelled' => 'danger',
                    }),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activo'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'weekly' => \App\Filament\Resources\Schedules\Pages\WeeklyScheduleView::route('/weekly'),
            'index' => ManageSchedules::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
