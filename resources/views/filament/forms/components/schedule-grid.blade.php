@php
    use App\Livewire\TeacherScheduleGrid;
@endphp

<div class="space-y-4">
    @if($showInCreate)
        <!-- Creating Mode Message -->
        <div class="bg-white p-4 rounded-lg shadow border">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">Configuración de Horarios</h3>
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <p class="text-sm text-yellow-800">
                    <strong>Importante:</strong> Primero guarda la información del profesor para poder configurar los horarios.
                    La grilla de horarios estará disponible después de crear el profesor.
                </p>
            </div>
            
            <!-- Preview Schedule Grid (disabled) -->
            <div class="opacity-60 pointer-events-none">
                <h4 class="text-md font-semibold mb-2 text-gray-700">Vista Previa de Grilla de Horarios</h4>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 text-center">La grilla estará disponible después de guardar el profesor</p>
                </div>
            </div>
        </div>
    @else
        <!-- Live Schedule Grid Component -->
        @livewire(TeacherScheduleGrid::class, ['teacher' => $teacher, 'isCreating' => false])
    @endif
</div>
