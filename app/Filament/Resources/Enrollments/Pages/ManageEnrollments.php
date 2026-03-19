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

                    // Asignar fecha de inscripción actual
                    $data['enrollment_date'] = now();

                    // Limpiar campos temporales que no existen en el modelo
                    unset($data['schedule_id']);

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
