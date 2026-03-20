<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Classroom;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected array $scheduleData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->scheduleData = [
            'branches' => Arr::get($data, 'schedule_branches', []),
            'slots' => Arr::get($data, 'schedule_slots', []),
            'instrument_id' => Arr::get($data, 'instrument_id'),
        ];

        unset($data['schedule_branches'], $data['schedule_slots'], $data['age_range']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $fullName = trim((string) Arr::get($data, 'name'));
            $baseUsername = Str::of($fullName)->lower()->replace(' ', '')->value();
            $username = $baseUsername !== '' ? $baseUsername : 'profesor';
            $counter = 1;

            while (User::where('username', $username)->exists()) {
                $username = "{$baseUsername}{$counter}";
                $counter++;
            }

            $user = User::create([
                'name' => $fullName,
                'username' => $username,
                'password' => Hash::make($fullName),
            ]);

            $teacherRole = Role::query()
                ->whereIn('name', ['profesor', 'teacher'])
                ->where('guard_name', 'web')
                ->orderByRaw("CASE WHEN name = 'profesor' THEN 0 ELSE 1 END")
                ->first();

            if ($teacherRole && $user->hasRole($teacherRole->name) === false) {
                $user->assignRole($teacherRole->name);
            }

            $record = static::getModel()::create([
                ...$data,
                'user_id' => $user->id,
                'username' => $username,
            ]);

            $record->instruments()->sync([(int) $this->scheduleData['instrument_id']]);

            return $record;
        });
    }

    protected function afterCreate(): void
    {
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $slots = (array) ($this->scheduleData['slots'] ?? []);
        $branches = (array) ($this->scheduleData['branches'] ?? []);
        $instrumentId = (int) ($this->scheduleData['instrument_id'] ?? 0);

        if ($instrumentId <= 0) {
            return;
        }

        foreach ($days as $day) {
            $branchId = (int) ($branches[$day] ?? 0);

            if ($branchId <= 0) {
                continue;
            }

            $classroomId = Classroom::query()
                ->where('branch_id', $branchId)
                ->orderBy('id')
                ->value('id');

            if (! $classroomId) {
                continue;
            }

            foreach ((array) ($slots[$day] ?? []) as $slotKey => $checked) {
                if (! $checked) {
                    continue;
                }

                $startTime = Carbon::createFromFormat('H_i', str_replace('h', '', (string) $slotKey))->format('H:i:s');
                $endTime = Carbon::parse($startTime)->addHour()->format('H:i:s');

                Schedule::create([
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'teacher_id' => $this->record->id,
                    'branch_id' => $branchId,
                    'classroom_id' => $classroomId,
                    'instrument_id' => $instrumentId,
                    'status' => 'available',
                    'is_active' => true,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
