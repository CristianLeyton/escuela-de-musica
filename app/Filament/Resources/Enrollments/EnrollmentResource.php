<?php

namespace App\Filament\Resources\Enrollments;

use App\Filament\Resources\Enrollments\Pages\ManageEnrollments;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Student;
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
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-clipboard-document-list';

    protected static ?string $navigationLabel = 'Inscripciones';

    protected static ?string $modelLabel = 'Inscripción';

    protected static ?string $pluralModelLabel = 'Inscripciones';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Seleccionar horario/turno
                Select::make('schedule_id')
                    ->label('Horario de clase')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return Schedule::with(['teacher.user', 'instrument', 'branch'])
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($schedule) {
                                $label = sprintf(
                                    '%s | %s - %s | %s | %s | Aula: %s',
                                    $schedule->day_of_week,
                                    $schedule->start_time->format('H:i'),
                                    $schedule->end_time->format('H:i'),
                                    $schedule->instrument->name,
                                    $schedule->branch->name,
                                    $schedule->classroom?->name ?? 'N/A'
                                );
                                return [$schedule->id => $label];
                            });
                    })
                    ->helperText('Selecciona el horario semanal para la inscripción'),

                // Seleccionar estudiante
                Select::make('student_id')
                    ->label('Estudiante')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return Student::with('user')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($student) {
                                $label = sprintf(
                                    '%s %s (%s)',
                                    $student->user->name,
                                    $student->user->lastname,
                                    $student->age_group
                                );
                                return [$student->id => $label];
                            });
                    }),

                // Campos adicionales
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->default('active')
                    ->options([
                        'active' => 'Activa',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),

                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('class_model_id')
                    ->numeric(),
                TextEntry::make('student_id')
                    ->numeric(),
                TextEntry::make('enrollment_date')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('grade')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(Enrollment $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('schedule.instrument.name')
                    ->label('Instrumento')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('schedule.day_of_week')
                    ->label('Día')
                    ->sortable(),

                TextColumn::make('schedule.start_time')
                    ->label('Horario')
                    ->formatStateUsing(fn($record) => $record->schedule?->start_time?->format('H:i') . ' - ' . $record->schedule?->end_time?->format('H:i'))
                    ->sortable(),

                TextColumn::make('student.user.name')
                    ->label('Estudiante')
                    ->formatStateUsing(fn($record) => $record->student->user->name . ' ' . $record->student->user->lastname)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('schedule.teacher.user.name')
                    ->label('Profesor')
                    ->formatStateUsing(fn($record) => $record->schedule?->teacher?->user?->name . ' ' . $record->schedule?->teacher?->user?->lastname)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('schedule.branch.name')
                    ->label('Sucursal')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de inscripción')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Activa',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('grade')
                    ->label('Calificación')
                    ->numeric()
                    ->sortable()
                    ->placeholder('-'),
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
            'index' => ManageEnrollments::route('/'),
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
