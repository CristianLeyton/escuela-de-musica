<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Classroom;
use App\Models\Schedule;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;
    protected array $scheduleData = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $timeKeys = ['h09_00', 'h10_00', 'h11_00', 'h12_00', 'h13_00', 'h14_00', 'h15_00', 'h16_00', 'h17_00'];

        $scheduleBranches = collect($days)->mapWithKeys(fn(string $day): array => [$day => null])->all();
        $scheduleSlots = [];

        foreach ($days as $day) {
            foreach ($timeKeys as $key) {
                $scheduleSlots[$day][$key] = false;
            }
        }

        foreach ($this->record->schedules()->get() as $schedule) {
            $day = (string) $schedule->day_of_week;
            $slotKey = 'h' . Carbon::parse($schedule->start_time)->format('H_i');

            if (! array_key_exists($day, $scheduleSlots) || ! array_key_exists($slotKey, $scheduleSlots[$day])) {
                continue;
            }

            $scheduleBranches[$day] ??= (int) $schedule->branch_id;
            $scheduleSlots[$day][$slotKey] = true;
        }

        $data['instrument_id'] = $this->record->instruments()->value('instruments.id');
        $data['age_range'] = "{$this->record->min_age}-{$this->record->max_age}";
        $data['schedule_branches'] = $scheduleBranches;
        $data['schedule_slots'] = $scheduleSlots;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->scheduleData = [
            'branches' => Arr::get($data, 'schedule_branches', []),
            'slots' => Arr::get($data, 'schedule_slots', []),
            'instrument_id' => (int) Arr::get($data, 'instrument_id'),
        ];

        unset($data['schedule_branches'], $data['schedule_slots'], $data['instrument_id'], $data['age_range']);

        return $data;
    }

    protected function afterSave(): void
    {
        $instrumentId = (int) ($this->scheduleData['instrument_id'] ?? 0);

        if ($instrumentId > 0) {
            $this->record->instruments()->sync([$instrumentId]);
        }

        Schedule::query()->where('teacher_id', $this->record->id)->delete();

        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $slots = (array) ($this->scheduleData['slots'] ?? []);
        $branches = (array) ($this->scheduleData['branches'] ?? []);

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
