<div class="space-y-4">
    <p class="text-sm text-gray-600">
        Selecciona una sede por cada día y marca los horarios disponibles del profesor.
    </p>

    @if (empty($branches))
        <div class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800">
            Debes crear al menos una sede activa para cargar horarios.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg bg-gray-50/50 p-2">
            <table class="min-w-full border-separate border-spacing-2 text-sm">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-left font-semibold text-gray-700">Hora</th>
                        @foreach ($days as $day)
                            <th class="px-2 py-2 text-center font-semibold text-gray-700">
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
                        <tr>
                            <td class="px-2 py-1.5 align-middle font-medium text-gray-700">{{ $slot['label'] }}</td>
                            @foreach ($days as $day)
                                @php
                                    $isChecked = (bool) data_get($this->data ?? [], "schedule_slots.{$day}.{$slot['key']}", false);
                                @endphp
                                <td class="p-0 align-middle">
                                    <label
                                        class="flex h-12 w-full cursor-pointer items-center justify-center rounded-2xl border border-transparent transition-colors shadow-sm {{ $isChecked ? 'bg-green-200 hover:bg-green-300 ring-1 ring-green-300/60' : 'bg-white hover:bg-gray-50 ring-1 ring-gray-200/80' }}"
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
