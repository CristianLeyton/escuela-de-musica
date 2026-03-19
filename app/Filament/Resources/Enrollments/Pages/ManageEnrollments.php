<?php

namespace App\Filament\Resources\Enrollments\Pages;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Schedule;
use Filament\Actions\CreateAction;
use Filament\Actions\StaticAction;
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

                    // Verificar que schedule_id existe
                    if (empty($data['schedule_id'])) {
                        Log::error('schedule_id está vacío');
                        Notification::make()
                            ->title('Error')
                            ->body('Debes seleccionar un horario para la inscripción')
                            ->danger()
                            ->send();
                        return $data;
                    }

                    // Verificar que el horario existe y está activo
                    $schedule = Schedule::find($data['schedule_id']);

                    if (!$schedule) {
                        Notification::make()
                            ->title('Error')
                            ->body('El horario seleccionado no existe')
                            ->danger()
                            ->send();
                        return $data;
                    }

                    if (!$schedule->is_active) {
                        Notification::make()
                            ->title('Error')
                            ->body('El horario seleccionado no está activo')
                            ->danger()
                            ->send();
                        return $data;
                    }

                    Log::info('Creando inscripción para schedule_id: ' . $data['schedule_id']);

                    // Verificar si el estudiante ya está inscrito en este horario
                    $existingEnrollment = \App\Models\Enrollment::where('schedule_id', $data['schedule_id'])
                        ->where('student_id', $data['student_id'])
                        ->first();

                    if ($existingEnrollment) {
                        Notification::make()
                            ->title('Error')
                            ->body('Este estudiante ya está inscrito en este horario')
                            ->danger()
                            ->send();
                        throw new \Illuminate\Http\Exceptions\HttpResponseException(
                            response()->json(['message' => 'Duplicate enrollment'], 422)
                        );
                    }

                    // Asignar status por defecto
                    $data['status'] = 'active';

                    // El schedule_id se mantiene para guardar en el modelo
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
