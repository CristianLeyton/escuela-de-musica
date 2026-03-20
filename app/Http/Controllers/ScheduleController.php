<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Instrument;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function getWeeklySchedule(Request $request)
    {
        $request->validate([
            'instrument_id' => 'nullable|exists:instruments,id',
            'branch_id' => 'nullable|exists:branches,id',
            'age' => 'nullable|integer|min:0|max:120',
        ]);

        $schedulesQuery = Schedule::with([
            'teacher.user',
            'branch',
            'classroom',
            'instrument',
            'enrollments.student.user',
        ]);

        if ($request->filled('instrument_id')) {
            $schedulesQuery->where('instrument_id', $request->instrument_id);
        }

        if ($request->filled('branch_id')) {
            $schedulesQuery->where('branch_id', $request->branch_id);
        }

        if ($request->filled('age')) {
            $age = (int) $request->age;
            $schedulesQuery->whereHas('teacher', function ($query) use ($age): void {
                $query->whereNotNull('min_age')
                    ->whereNotNull('max_age')
                    ->where('min_age', '<=', $age)
                    ->where('max_age', '>=', $age);
            });
        }

        $schedules = $schedulesQuery->get();

        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $availableHours = [];
        $buckets = [];

        foreach ($schedules as $schedule) {
            if (! in_array($schedule->day_of_week, $daysOfWeek, true)) {
                continue;
            }

            $startTime = $schedule->start_time->format('H:i');

            if (! in_array($startTime, $availableHours, true)) {
                $availableHours[] = $startTime;
            }

            $key = $schedule->day_of_week.'|'.$startTime.'|'.$schedule->instrument_id;
            $buckets[$key][] = $schedule;
        }

        $gridData = [];

        foreach ($buckets as $group) {
            $first = $group[0];
            $day = $first->day_of_week;
            $startTime = $first->start_time->format('H:i');

            $branchNames = collect($group)
                ->map(fn (Schedule $s) => $s->branch?->name)
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->implode(', ');

            $offerings = collect($group)
                ->map(function (Schedule $sch) {
                    return [
                        'id' => $sch->id,
                        'teacher' => self::formatTeacherLabel($sch),
                        'branch' => $sch->branch?->name ?? '—',
                        'enrolled_count' => $sch->enrollments->where('status', 'active')->count(),
                    ];
                })
                ->values()
                ->all();

            $gridData[$day][$startTime][] = [
                'instrument' => $first->instrument?->name ?? 'Instrumento',
                'instrument_id' => $first->instrument_id,
                'branches' => $branchNames,
                'offerings' => $offerings,
            ];
        }

        sort($availableHours);

        $filters = [
            'instruments' => Instrument::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'branches' => Branch::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'grid' => $gridData,
                'available_hours' => $availableHours,
                'days_of_week' => $daysOfWeek,
            ],
            'filters' => $filters,
        ]);
    }

    public function getScheduleDetails($id)
    {
        $schedule = Schedule::with([
            'teacher.user',
            'branch',
            'classroom',
            'instrument',
            'enrollments.student.user',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    protected static function formatTeacherLabel(Schedule $schedule): string
    {
        $teacher = $schedule->teacher;
        if (! $teacher) {
            return 'Sin profesor';
        }

        $user = $teacher->user;
        if ($user) {
            $name = trim(($user->name ?? '').' '.($user->lastname ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        return $teacher->name ?? 'Sin nombre';
    }
}
