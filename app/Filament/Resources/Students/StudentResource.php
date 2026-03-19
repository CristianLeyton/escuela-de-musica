<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\ManageStudents;
use App\Models\Student;
use App\Models\User;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $recordTitleAttribute = 'user.name';

    protected static ?string $navigationLabel = 'Alumnos';

    protected static ?string $modelLabel = 'Alumno';

    protected static ?string $pluralModelLabel = 'Alumnos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return User::where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $user = User::find($value);
                        return $user ? $user->name . ' ' . $user->lastname : $value;
                    })
                    ->required()
                    ->label('Usuario'),
                DatePicker::make('birth_date')
                    ->required()
                    ->label('Fecha de Nacimiento'),
                Select::make('age_group')
                    ->required()
                    ->label('Grupo Etario')
                    ->options([
                        'niño' => 'Niño',
                        'adolescente' => 'Adolescente',
                        'adulto' => 'Adulto',
                    ]),
                TextInput::make('phone')
                    ->tel()
                    ->label('Teléfono'),
                Textarea::make('emergency_contact')
                    ->columnSpanFull()
                    ->label('Contacto de Emergencia'),
                Textarea::make('medical_notes')
                    ->columnSpanFull()
                    ->label('Notas Médicas'),
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
                TextEntry::make('user.name')
                    ->label('Nombre'),
                TextEntry::make('user.email')
                    ->label('Email'),
                TextEntry::make('birth_date')
                    ->date()
                    ->label('Fecha de Nacimiento'),
                TextEntry::make('age_group')
                    ->label('Grupo Etario'),
                TextEntry::make('phone')
                    ->placeholder('-')
                    ->label('Teléfono'),
                TextEntry::make('emergency_contact')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label('Contacto de Emergencia'),
                TextEntry::make('medical_notes')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label('Notas Médicas'),
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
                    ->visible(fn(Student $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->date()
                    ->sortable()
                    ->label('Fecha Nac.'),
                TextColumn::make('age_group')
                    ->searchable()
                    ->label('Grupo Etario'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('Teléfono'),
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
            'index' => ManageStudents::route('/'),
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
