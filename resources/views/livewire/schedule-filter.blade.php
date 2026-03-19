<div>
    <!-- Header con título y filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Horarios Semanales</h2>
            <div class="text-sm text-gray-600">
                @if (!empty($dateRange))
                    Del {{ \Carbon\Carbon::parse($dateRange['start'])->format('d/m/Y') }}
                    al {{ \Carbon\Carbon::parse($dateRange['end'])->format('d/m/Y') }}
                @endif
            </div>
        </div>

        <!-- Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Profesor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profesor</label>
                <select wire:model.live="teacher_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los profesores</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Estudiante -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estudiante</label>
                <select wire:model.live="student_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los estudiantes</option>
                    @foreach ($students as $student)
                        <option value="{{ $student['id'] }}">{{ $student['name'] }} ({{ $student['age_group'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Instrumento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Instrumento</label>
                <select wire:model.live="instrument_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los instrumentos</option>
                    @foreach ($instruments as $instrument)
                        <option value="{{ $instrument['id'] }}">{{ $instrument['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sede -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                <select wire:model.live="branch_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las sedes</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Grupo Etario -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grupo Etario</label>
                <select wire:model.live="age_group"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los grupos</option>
                    @foreach ($ageGroups as $ageGroup)
                        <option value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Inicio -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" wire:model.live="start_date" value="{{ $start_date }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Fecha Fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" wire:model.live="end_date" value="{{ $end_date }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Botón Limpiar -->
            <div class="flex items-end">
                <button wire:click="clearFilters"
                    class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    @if ($loading)
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span class="ml-2 text-gray-600">Cargando horarios...</span>
        </div>
    @else
        <!-- Grid de Horarios -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if (empty($availableHours))
                <div class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-lg font-medium">No hay clases para los filtros seleccionados</p>
                    <p class="text-sm mt-2">Intenta ajustar los filtros o el rango de fechas</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Hora
                                </th>
                                @foreach ($daysOfWeek as $day)
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-32">
                                        {{ $day }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($availableHours as $hour)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $hour }}
                                    </td>
                                    @foreach ($daysOfWeek as $day)
                                        <td class="px-2 py-2 border-l border-gray-200">
                                            @php
                                                $classes = $this->getClassesForCell($day, $hour);
                                            @endphp

                                            @if (empty($classes))
                                                <!-- Celda vacía - disponible -->
                                                <div
                                                    class="h-16 bg-green-50 rounded border border-green-200 flex items-center justify-center">
                                                    <span class="text-xs text-green-600">Disponible</span>
                                                </div>
                                            @else
                                                <!-- Celdas con clases -->
                                                <div class="space-y-1">
                                                    @foreach ($classes as $class)
                                                        <div wire:click="showClassDetails({{ $class['id'] }})"
                                                            class="h-16 bg-indigo-50 rounded border border-indigo-200 p-2 cursor-pointer hover:bg-indigo-100 transition-colors">
                                                            <div class="text-xs font-semibold text-indigo-900 truncate">
                                                                {{ $class['instrument'] }}
                                                            </div>
                                                            <div class="text-xs text-indigo-700 truncate">
                                                                {{ $class['teacher'] }}
                                                            </div>
                                                            <div class="text-xs text-gray-600 truncate">
                                                                {{ $class['branch'] }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- Modal de Detalles de Clase -->
    @if ($showClassModal && $selectedClass)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
                wire:click.stop>
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Detalles de la Clase</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Información básica -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Día</label>
                                <p class="text-sm text-gray-900">{{ $selectedClass['day_of_week'] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Horario</label>
                                <p class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($selectedClass['start_time'])->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($selectedClass['end_time'])->format('H:i') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Instrumento</label>
                                <p class="text-sm text-gray-900">{{ $selectedClass['instrument']['name'] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Profesor</label>
                                <p class="text-sm text-gray-900">
                                    {{ $selectedClass['teacher']['user']['name'] }}
                                    {{ $selectedClass['teacher']['user']['lastname'] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sede</label>
                                <p class="text-sm text-gray-900">{{ $selectedClass['branch']['name'] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Aula</label>
                                <p class="text-sm text-gray-900">
                                    {{ $selectedClass['classroom']['name'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <p class="text-sm text-gray-900">
                                    @switch($selectedClass['status'])
                                        @case('available')
                                            <span class="text-green-600">Disponible</span>
                                        @break

                                        @case('occupied')
                                            <span class="text-yellow-600">Ocupado</span>
                                        @break

                                        @case('cancelled')
                                            <span class="text-red-600">Cancelado</span>
                                        @break

                                        @default
                                            <span class="text-gray-600">{{ $selectedClass['status'] }}</span>
                                    @endswitch
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Activo</label>
                                <p class="text-sm text-gray-900">
                                    {{ $selectedClass['is_active'] ? 'Sí' : 'No' }}
                                </p>
                            </div>
                        </div>

                        <!-- Estudiantes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estudiantes
                                Inscriptos</label>
                            @if (isset($selectedClass['classes']) && count($selectedClass['classes']) > 0)
                                @foreach ($selectedClass['classes'] as $class)
                                    @if ($class['students']->count() > 0)
                                        <div class="space-y-2">
                                            @foreach ($class['students'] as $student)
                                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                                    <span class="text-sm text-gray-900">{{ $student['user']['name'] }}
                                                        {{ $student['user']['lastname'] }}</span>
                                                    <span
                                                        class="text-xs text-gray-600">{{ $student['age_group'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500">No hay estudiantes inscriptos</p>
                            @endif
                        </div>

                        <!-- Notas -->
                        @if ($selectedClass['notes'])
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notas</label>
                                <p class="text-sm text-gray-900">{{ $selectedClass['notes'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
