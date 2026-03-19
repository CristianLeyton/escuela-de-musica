<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Instrument;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos existentes
        $branches = Branch::all();
        $teachers = Teacher::with('user')->get();
        $classrooms = Classroom::all();
        $instruments = Instrument::all();

        // Crear horarios para cada profesor
        foreach ($teachers as $teacher) {
            // Cada profesor tendrá 3-5 horarios diferentes
            $numSchedules = rand(3, 5);
            
            for ($i = 0; $i < $numSchedules; $i++) {
                $branch = $branches->random();
                $classroom = $classrooms->where('branch_id', $branch->id)->random();
                $instrument = $instruments->random();
                
                // Días de la semana disponibles
                $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                $day = $daysOfWeek[array_rand($daysOfWeek)];
                
                // Horarios aleatorios
                $timeSlots = [
                    ['start' => '09:00', 'end' => '10:00'],
                    ['start' => '10:00', 'end' => '11:00'],
                    ['start' => '11:00', 'end' => '12:00'],
                    ['start' => '14:00', 'end' => '15:00'],
                    ['start' => '15:00', 'end' => '16:00'],
                    ['start' => '16:00', 'end' => '17:00'],
                    ['start' => '17:00', 'end' => '18:00'],
                ];
                
                $timeSlot = $timeSlots[array_rand($timeSlots)];
                
                // Estados posibles
                $statuses = ['available', 'occupied', 'cancelled'];
                $status = $statuses[array_rand($statuses)];
                
                Schedule::create([
                    'day_of_week' => $day,
                    'start_time' => $timeSlot['start'],
                    'end_time' => $timeSlot['end'],
                    'teacher_id' => $teacher->id,
                    'branch_id' => $branch->id,
                    'classroom_id' => $classroom->id,
                    'instrument_id' => $instrument->id,
                    'status' => $status,
                    'notes' => "Clase de {$instrument->name} con {$teacher->user->name} {$teacher->user->lastname}",
                    'is_active' => $status !== 'cancelled',
                ]);
            }
        }

        // Crear algunos horarios adicionales sin profesor asignado (disponibles)
        for ($i = 0; $i < 10; $i++) {
            $branch = $branches->random();
            $classroom = $classrooms->where('branch_id', $branch->id)->random();
            $instrument = $instruments->random();
            
            $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
            $day = $daysOfWeek[array_rand($daysOfWeek)];
            
            $timeSlots = [
                ['start' => '09:00', 'end' => '10:00'],
                ['start' => '10:00', 'end' => '11:00'],
                ['start' => '11:00', 'end' => '12:00'],
                ['start' => '14:00', 'end' => '15:00'],
                ['start' => '15:00', 'end' => '16:00'],
                ['start' => '16:00', 'end' => '17:00'],
                ['start' => '17:00', 'end' => '18:00'],
            ];
            
            $timeSlot = $timeSlots[array_rand($timeSlots)];
            
            Schedule::create([
                'day_of_week' => $day,
                'start_time' => $timeSlot['start'],
                'end_time' => $timeSlot['end'],
                'teacher_id' => $teachers->random()->id,
                'branch_id' => $branch->id,
                'classroom_id' => $classroom->id,
                'instrument_id' => $instrument->id,
                'status' => 'available',
                'notes' => "Horario disponible para {$instrument->name}",
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Horarios creados exitosamente');
        $this->command->info('📅 Total de horarios creados: ' . Schedule::count());
        $this->command->info('🎯 Horarios por profesor:');
        
        foreach ($teachers as $teacher) {
            $scheduleCount = Schedule::where('teacher_id', $teacher->id)->count();
            $this->command->info("   - {$teacher->user->name} {$teacher->user->lastname}: {$scheduleCount} horarios");
        }
    }
}
