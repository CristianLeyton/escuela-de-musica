<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\ManageTeachers;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\Instrument;
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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?string $recordTitleAttribute = 'user.name';

    protected static ?string $navigationLabel = 'Profesores';

    protected static ?string $modelLabel = 'Profesor';

    protected static ?string $pluralModelLabel = 'Profesores';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $timeSlots = [
            ['key' => 'h09_00', 'label' => '09:00'],
            ['key' => 'h10_00', 'label' => '10:00'],
            ['key' => 'h11_00', 'label' => '11:00'],
            ['key' => 'h12_00', 'label' => '12:00'],
            ['key' => 'h13_00', 'label' => '13:00'],
            ['key' => 'h14_00', 'label' => '14:00'],
            ['key' => 'h15_00', 'label' => '15:00'],
            ['key' => 'h16_00', 'label' => '16:00'],
            ['key' => 'h17_00', 'label' => '17:00'],
        ];

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre completo del profesor')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('instrument_id')
                    ->label('Instrumento que enseñará')
                    ->options(
                        Instrument::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('age_range')
                    ->label('Rango de edad')
                    ->options([
                        '4-6' => '4 a 6 años',
                        '7-10' => '7 a 10 años',
                        '7-17' => '7 a 17 años',
                        '11-14' => '11 a 14 años',
                        '15-17' => '15 a 17 años',
                        '18-99' => '18 años en adelante',
                    ])
                    ->default('7-10')
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        [$minAge, $maxAge] = array_map('intval', explode('-', (string) $state));
                        $set('min_age', $minAge);
                        $set('max_age', $maxAge);
                    }),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),

                Hidden::make('min_age')
                    ->default(7),
                Hidden::make('max_age')
                    ->default(10),

                Hidden::make('schedule_branches')
                    ->default(function () use ($days): array {
                        $firstBranchId = Branch::query()->orderBy('name')->value('id');

                        return collect($days)->mapWithKeys(
                            fn(string $day): array => [$day => $firstBranchId]
                        )->all();
                    }),

                Hidden::make('schedule_slots')
                    ->default(function () use ($days, $timeSlots): array {
                        $default = [];

                        foreach ($days as $day) {
                            foreach ($timeSlots as $slot) {
                                $default[$day][$slot['key']] = false;
                            }
                        }

                        return $default;
                    }),

                View::make('filament.forms.components.teacher-schedule-wizard-grid')
                    ->viewData([
                        'days' => $days,
                        'timeSlots' => $timeSlots,
                        'branches' => Branch::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nombre'),
                TextEntry::make('username')
                    ->label('Usuario'),
                TextEntry::make('instruments.name')
                    ->label('Instrumentos')
                    ->bulleted(),
                TextEntry::make('min_age')
                    ->label('Edad Mínima')
                    ->numeric(),
                TextEntry::make('max_age')
                    ->label('Edad Máxima')
                    ->numeric(),
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
                    ->visible(fn(Teacher $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('username')
                    ->label('Usuario')
                    ->searchable(),
                TextColumn::make('instruments.name')
                    ->label('Instrumentos')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return is_array($state) ? implode(', ', $state) : $state;
                    }),
                TextColumn::make('min_age')
                    ->numeric()
                    ->sortable()
                    ->label('Edad Mín.'),
                TextColumn::make('max_age')
                    ->numeric()
                    ->sortable()
                    ->label('Edad Máx.'),
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
            'index' => ManageTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
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
