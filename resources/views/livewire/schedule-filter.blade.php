<div>
    <!-- Header con título y filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Horarios Semanales</h2>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Edad</label>
                <select wire:model.live="age_group"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los grupos</option>
                    @foreach ($ageGroups as $ageGroup)
                        <option value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                    @endforeach
                </select>
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
                    <p class="text-sm mt-2">Intenta ajustar los filtros</p>
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
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-opacity duration-300"
            wire:click="closeModal">
            <div class="relative top-20 mx-auto p-4 w-11/12 md:w-2/3 lg:w-1/2 max-w-2xl" wire:click.stop>
                <div class="bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                    <!-- Header con color de acento -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ $selectedClass['instrument']['name'] }}
                                    </h3>
                                    <p class="text-indigo-200 text-sm">{{ $selectedClass['day_of_week'] }} •
                                        {{ \Carbon\Carbon::parse($selectedClass['start_time'])->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($selectedClass['end_time'])->format('H:i') }}</p>
                                </div>
                            </div>
                            <button wire:click="closeModal"
                                class="bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Información del Profesor y Ubicación -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-start space-x-3 bg-gray-50 p-4 rounded-lg">
                                <div class="bg-indigo-100 p-2 rounded-lg shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Profesor</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ $selectedClass['teacher']['user']['name'] }}
                                        {{ $selectedClass['teacher']['user']['lastname'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 bg-gray-50 p-4 rounded-lg">
                                <div class="bg-emerald-100 p-2 rounded-lg shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ubicación</label>
                                    <p class="text-gray-900 font-medium">{{ $selectedClass['branch']['name'] }}</p>
                                    <p class="text-sm text-gray-600">Aula:
                                        {{ $selectedClass['classroom']['name'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Estado del horario -->
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-700">Estado del horario:</span>
                                @switch($selectedClass['status'])
                                    @case('available')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Disponible
                                        </span>
                                    @break

                                    @case('occupied')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                            Ocupado
                                        </span>
                                    @break

                                    @case('cancelled')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Cancelado
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            {{ $selectedClass['status'] }}
                                        </span>
                                @endswitch
                            </div>
                            @if (!$selectedClass['is_active'])
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Inactivo
                                </span>
                            @endif
                        </div>

                        <!-- Estudiantes Inscriptos -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Estudiantes Inscriptos
                                    @if (isset($selectedClass['enrollments']))
                                        <span
                                            class="ml-2 bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                            {{ count(array_filter($selectedClass['enrollments'], fn($e) => $e['student'])) }}
                                        </span>
                                    @endif
                                </h4>
                            </div>

                            @if (isset($selectedClass['enrollments']) && count($selectedClass['enrollments']) > 0)
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach ($selectedClass['enrollments'] as $enrollment)
                                        @if ($enrollment['student'])
                                            <div
                                                class="flex items-center justify-between bg-white border border-gray-200 p-3 rounded-lg hover:shadow-md transition-shadow">
                                                <div class="flex items-center space-x-3">
                                                    <div
                                                        class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold">
                                                        {{ substr($enrollment['student']['user']['name'], 0, 1) }}{{ substr($enrollment['student']['user']['lastname'], 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $enrollment['student']['user']['name'] }}
                                                            {{ $enrollment['student']['user']['lastname'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $enrollment['student']['age_group'] }}</p>
                                                    </div>
                                                </div>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enrollment['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $enrollment['status'] === 'active' ? 'Activa' : $enrollment['status'] }}
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">No hay estudiantes inscriptos</p>
                                    <p class="text-sm text-gray-400 mt-1">Este horario está disponible para nuevas
                                        inscripciones</p>
                                </div>
                            @endif
                        </div>

                        <!-- Notas -->
                        @if ($selectedClass['notes'])
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <div>
                                        <label class="text-sm font-semibold text-amber-800">Notas</label>
                                        <p class="text-sm text-amber-700 mt-1">{{ $selectedClass['notes'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Botón de acción -->
                        <div class="mt-6 flex justify-end">
                            <button wire:click="closeModal"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-6 py-2.5 rounded-lg transition-colors duration-200">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
