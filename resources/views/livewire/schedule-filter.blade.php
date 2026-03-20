@php
    $gcalPalette = [
        ['bg' => '#4285F4', 'border' => '#1967D2', 'text' => '#ffffff'],
        ['bg' => '#EA4335', 'border' => '#C5221F', 'text' => '#ffffff'],
        ['bg' => '#FBBC04', 'border' => '#F9AB00', 'text' => '#3c4043'],
        ['bg' => '#34A853', 'border' => '#137333', 'text' => '#ffffff'],
        ['bg' => '#A142F4', 'border' => '#7627BB', 'text' => '#ffffff'],
        ['bg' => '#F439A0', 'border' => '#C1175A', 'text' => '#ffffff'],
        ['bg' => '#00ACC1', 'border' => '#00838F', 'text' => '#ffffff'],
        ['bg' => '#FF6D00', 'border' => '#E65100', 'text' => '#ffffff'],
    ];
@endphp

<div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Horarios Semanales</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Instrumento</label>
                <select wire:model.live="instrument_id"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 transition-colors">
                    @forelse ($instruments as $instrument)
                        <option value="{{ $instrument['id'] }}">{{ $instrument['name'] }}</option>
                    @empty
                        <option value="" disabled>No hay instrumentos activos</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                <select wire:model.live="branch_id"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 transition-colors">
                    <option value="">Todas las sedes</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Edad (filtra por rango del profesor)</label>
                <input type="number" wire:model.live.debounce.400ms="age" min="0" max="120"
                    placeholder="Ej: 10"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 transition-colors" />
            </div>

            <div class="flex items-end">
                <button wire:click="clearFilters" type="button"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Restablecer filtros</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    @if ($loading)
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Cargando horarios...</span>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if (empty($availableHours))
                <div class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-900">No hay clases para los filtros seleccionados</p>
                    <p class="text-sm mt-2 text-gray-500">Intenta ajustar los filtros</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Hora
                                </th>
                                @foreach ($daysOfWeek as $day)
                                    <th
                                        class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[8.5rem]">
                                        {{ $day }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-[#f8f9fa] divide-y divide-gray-200">
                            @foreach ($availableHours as $hour)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-800">
                                        {{ $hour }}
                                    </td>
                                    @foreach ($daysOfWeek as $day)
                                        <td class="px-2 py-2 border-l border-gray-200 align-top">
                                            @php
                                                $classes = $this->getClassesForCell($day, $hour);
                                            @endphp

                                            @if (empty($classes))
                                                <div
                                                    class="min-h-[3.5rem] rounded-lg border border-dashed border-gray-300 bg-white/80 flex items-center justify-center hover:bg-white transition-colors">
                                                    <span class="text-[11px] font-medium text-gray-400"></span>
                                                </div>
                                            @else
                                                <div class="space-y-1.5">
                                                    @foreach ($classes as $class)
                                                        @php
                                                            $ci =
                                                                ((int) ($class['instrument_id'] ?? 0)) %
                                                                count($gcalPalette);
                                                            $pal = $gcalPalette[$ci];
                                                            $payload = [
                                                                'instrument' => $class['instrument'] ?? '',
                                                                'day' => $day,
                                                                'hour' => $hour,
                                                                'offerings' => $class['offerings'] ?? [],
                                                            ];
                                                        @endphp
                                                        <div @click="$wire.openCellModal({{ \Illuminate\Support\Js::from($payload) }})"
                                                            class="min-h-[3.5rem] rounded-lg border-2 p-2 cursor-pointer shadow-sm hover:brightness-105 hover:shadow transition-all"
                                                            style="background-color: {{ $pal['bg'] }}; border-color: {{ $pal['border'] }}; color: {{ $pal['text'] }};">
                                                            <div class="text-xs font-bold leading-tight truncate">
                                                                {{ $class['instrument'] }}
                                                            </div>
                                                            <div class="text-[11px] font-medium leading-tight opacity-95 truncate mt-0.5"
                                                                title="{{ $class['branches'] ?? '' }}">
                                                                {{ $class['branches'] ?? '' }}
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

    @if ($showClassModal)
        <div class="fixed inset-0 bg-gray-950/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-opacity duration-300"
            wire:click="closeModal">
            <div class="relative top-16 mx-auto p-4 w-11/12 md:w-3/4 lg:w-2/3 max-w-4xl" wire:click.stop>
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-start gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $modalInstrument }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $modalDay }} · {{ $modalHour }}
                            </p>
                        </div>
                        <button wire:click="closeModal" type="button"
                            class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Clases disponibles para este instrumento en este horario:</p>
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="text-left font-semibold px-4 py-3">Profesor</th>
                                        <th class="text-left font-semibold px-4 py-3">Sede</th>
                                        <th class="text-right font-semibold px-4 py-3">Alumnos inscriptos</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($modalOfferings as $row)
                                        <tr class="hover:bg-gray-50/80">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row['teacher'] ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">{{ $row['branch'] ?? '—' }}</td>
                                            <td class="px-4 py-3 text-right tabular-nums text-gray-900">
                                                {{ (int) ($row['enrolled_count'] ?? 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">No hay ofertas en
                                                este horario.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button wire:click="closeModal" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
