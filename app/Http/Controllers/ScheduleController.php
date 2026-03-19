<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Instrument;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function getWeeklySchedule(Request $request)
    {
        $request->validate([
            'teacher_id' => 'nullable|exists:teachers,id',
            'instrument_id' => 'nullable|exists:instruments,id',
            'age_group' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Query base para schedules
        $schedulesQuery = Schedule::with([
            'teacher.user',
            'branch',
            'classroom',
            'instrument'
        ]);

        // Aplicar filtros
        if ($request->teacher_id) {
            $schedulesQuery->where('teacher_id', $request->teacher_id);
        }

        if ($request->instrument_id) {
            $schedulesQuery->where('instrument_id', $request->instrument_id);
        }

        if ($request->branch_id) {
            $schedulesQuery->where('branch_id', $request->branch_id);
        }

        // Filtro por grupo etario (a través de enrollments ahora)
        if ($request->age_group) {
            $schedulesQuery->whereHas('enrollments.student', function ($query) use ($request) {
                $query->where('students.age_group', $request->age_group);
            });
        }

        // Mostrar también horarios inactivos para que se vean en el grid
        $schedules = $schedulesQuery->get();

        // Estructurar datos para el grid
        $gridData = [];
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $availableHours = [];

        foreach ($schedules as $schedule) {
            $dayIndex = array_search($schedule->day_of_week, $daysOfWeek);
            if ($dayIndex === false) continue;

            $startTime = $schedule->start_time->format('H:i');
            $endTime = $schedule->end_time->format('H:i');

            // Agregar hora a las disponibles
            if (!in_array($startTime, $availableHours)) {
                $availableHours[] = $startTime;
            }

            // Crear entrada para el schedule
            $gridData[$schedule->day_of_week][$startTime][] = [
                'id' => $schedule->id,
                'schedule_id' => $schedule->id,
                'teacher' => $schedule->teacher->user->name . ' ' . $schedule->teacher->user->lastname,
                'instrument' => $schedule->instrument->name,
                'branch' => $schedule->branch->name,
                'classroom' => $schedule->classroom->name ?? 'N/A',
                'students' => [],
                'age_groups' => [],
                'status' => $schedule->status,
                'duration_minutes' => $schedule->start_time->diffInMinutes($schedule->end_time),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_active' => $schedule->is_active,
            ];
        }

        // Ordenar horas
        sort($availableHours);

        // Obtener datos para filtros
        $filters = [
            'teachers' => Teacher::with('user')->where('is_active', true)->get()->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->name . ' ' . $teacher->user->lastname,
                ];
            }),
            'instruments' => Instrument::where('is_active', true)->get(['id', 'name']),
            'branches' => Branch::where('is_active', true)->get(['id', 'name']),
            'age_groups' => Student::where('is_active', true)->distinct()->pluck('age_group')->filter(),
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
            'enrollments.student.user'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }
}
