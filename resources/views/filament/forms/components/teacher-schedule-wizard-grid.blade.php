<div class="space-y-4">
    <p class="text-sm text-gray-600">
        Selecciona una sede por cada día y marca los horarios disponibles del profesor.
    </p>

    @if (empty($branches))
        <div class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800">
            Debes crear al menos una sede activa para cargar horarios.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border-b border-r border-gray-200 px-3 py-2 text-left font-semibold text-gray-700">Hora</th>
                        @foreach ($days as $day)
                            <th class="border-b border-gray-200 px-3 py-2 text-center font-semibold text-gray-700">
                                <div class="space-y-2">
                                    <div>{{ $day }}</div>
                                    <select
                                        wire:model.live="data.schedule_branches.{{ $day }}"
                                        class="w-full rounded-md border-gray-300 text-xs"
                                    >
                                        @foreach ($branches as $branchId => $branchName)
                                            <option value="{{ $branchId }}">{{ $branchName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timeSlots as $slot)
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="border-r border-gray-200 px-3 py-2 font-medium text-gray-700">{{ $slot['label'] }}</td>
                            @foreach ($days as $day)
                                @php
                                    $isChecked = (bool) data_get($this->data ?? [], "schedule_slots.{$day}.{$slot['key']}", false);
                                @endphp
                                <td class="border border-gray-200 p-0">
                                    <label
                                        class="flex h-12 w-full cursor-pointer items-center justify-center transition-colors {{ $isChecked ? 'bg-green-200 hover:bg-green-300' : 'bg-white hover:bg-gray-100' }}"
                                    >
                                        <input
                                            type="checkbox"
                                            wire:model.live="data.schedule_slots.{{ $day }}.{{ $slot['key'] }}"
                                            class="sr-only"
                                        />
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
