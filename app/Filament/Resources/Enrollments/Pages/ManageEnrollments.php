<?php

namespace App\Filament\Resources\Enrollments\Pages;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Schedule;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ManageEnrollments extends ManageRecords
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    Log::info('Datos recibidos del formulario:', $data);

                    // Verificar que class_date existe
                    if (empty($data['class_date'])) {
                        Log::error('class_date está vacío');
                        Notification::make()
                            ->title('Error')
                            ->body('Debes seleccionar una fecha para la clase')
                            ->danger()
                            ->send();
                        return $data;
                    }

                    // Crear la clase (ClassModel) primero
                    $schedule = Schedule::find($data['schedule_id']);

                    if (!$schedule) {
                        Notification::make()
                            ->title('Error')
                            ->body('El horario seleccionado no existe')
                            ->danger()
                            ->send();
                        return $data;
                    }

                    Log::info('Creando ClassModel con fecha: ' . $data['class_date']);

                    // Crear la clase específica para esta fecha
                    $classModel = ClassModel::create([
                        'schedule_id' => $schedule->id,
                        'class_date' => $data['class_date'],
                        'duration_minutes' => $schedule->start_time->diffInMinutes($schedule->end_time),
                        'status' => 'scheduled',
                    ]);

                    Log::info('ClassModel creado:', ['id' => $classModel->id, 'date' => $classModel->class_date]);

                    // Agregar el ID de la clase creada a los datos de inscripción
                    $data['class_model_id'] = $classModel->id;
                    $data['enrollment_date'] = now();

                    // Limpiar campos temporales que no existen en el modelo
                    unset($data['schedule_id']);
                    unset($data['class_date']);

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->title('Inscripción creada')
                        ->body('El estudiante ha sido inscrito exitosamente')
                        ->success()
                ),
        ];
    }
}
