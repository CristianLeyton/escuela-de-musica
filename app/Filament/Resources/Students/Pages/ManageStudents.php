<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Student::class)
                ->label('Crear alumno')
                ->form([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('lastname')
                        ->label('Apellido')
                        ->required()
                        ->maxLength(120),
                    DatePicker::make('birth_date')
                        ->label('Fecha de Nacimiento')
                        ->required(),
                    TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel(),
                    Textarea::make('emergency_contact')
                        ->label('Contacto de Emergencia')
                        ->columnSpanFull(),
                    Textarea::make('medical_notes')
                        ->label('Notas Médicas')
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Activo')
                        ->default(true),
                ])
                ->using(function (array $data): Student {
                    return DB::transaction(function () use ($data): Student {
                        $name = trim((string) ($data['name'] ?? ''));
                        $lastname = trim((string) ($data['lastname'] ?? ''));

                        $base = Str::of($name.$lastname)->lower()->replace(' ', '')->value();
                        if ($base === '') {
                            $base = 'alumno';
                        }

                        $username = $base;
                        $counter = 1;
                        while (User::query()->where('username', $username)->exists()) {
                            $username = $base.$counter;
                            $counter++;
                        }

                        $user = User::query()->create([
                            'name' => $name,
                            'lastname' => $lastname,
                            'username' => $username,
                            'password' => Hash::make($name.$lastname),
                            'email' => null,
                        ]);

                        $studentRole = Role::query()
                            ->whereIn('name', ['alumno', 'student'])
                            ->where('guard_name', 'web')
                            ->orderByRaw("CASE WHEN name = 'alumno' THEN 0 ELSE 1 END")
                            ->first();

                        if ($studentRole && $user->hasRole($studentRole->name) === false) {
                            $user->assignRole($studentRole->name);
                        }

                        return Student::query()->create([
                            'user_id' => $user->id,
                            'birth_date' => $data['birth_date'],
                            'phone' => $data['phone'] ?? null,
                            'emergency_contact' => $data['emergency_contact'] ?? null,
                            'medical_notes' => $data['medical_notes'] ?? null,
                            'is_active' => (bool) ($data['is_active'] ?? true),
                        ]);
                    });
                }),
        ];
    }
}
