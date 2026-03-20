<style>
    .schedule-grid-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .schedule-card {
        background-color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .schedule-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #1f2937;
    }

    .schedule-warning {
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: #fefce8;
        border: 1px solid #fde047;
        border-radius: 0.375rem;
    }

    .schedule-warning-text {
        font-size: 0.875rem;
        color: #92400e;
    }

    .schedule-preview {
        opacity: 0.6;
        pointer-events: none;
    }

    .schedule-preview-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .schedule-preview-box {
        background-color: #f9fafb;
        padding: 1rem;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
    }

    .schedule-preview-text {
        font-size: 0.875rem;
        color: #6b7280;
        text-align: center;
    }

    .schedule-form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .schedule-form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        outline: none;
    }

    .schedule-form-select:focus {
        ring: 2px;
        ring-color: #3b82f6;
    }

    .schedule-button-primary {
        padding: 0.5rem 1rem;
        background-color: #2563eb;
        color: white;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .schedule-button-primary:hover {
        background-color: #1d4ed8;
    }

    .schedule-button-secondary {
        padding: 0.5rem 1rem;
        background-color: #6b7280;
        color: white;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .schedule-button-secondary:hover {
        background-color: #4b5563;
    }

    .schedule-instructions {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .schedule-table-container {
        background-color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow-x: auto;
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .schedule-table-header {
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        padding: 0.5rem 0.5rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .schedule-table-header-center {
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        padding: 0.5rem 0.5rem;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .schedule-table-cell {
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        padding: 0.5rem 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .schedule-table-button-cell {
        border: 1px solid #d1d5db;
        padding: 0;
        text-align: center;
    }

    .schedule-table-button {
        width: 100%;
        height: 100%;
        padding: 0.5rem 0.5rem;
        font-size: 0.75rem;
        transition: background-color 0.2s;
        cursor: pointer;
        border: none;
        background: none;
    }

    .schedule-table-button:hover {
        opacity: 0.8;
    }

    .schedule-legend {
        background-color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .schedule-legend-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .schedule-legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .schedule-legend-color {
        width: 1rem;
        height: 1rem;
        border: 1px solid #d1d5db;
    }

    .schedule-legend-available {
        background-color: #f3f4f6;
    }

    .schedule-legend-selected {
        background-color: #3b82f6;
        color: white;
    }

    .schedule-legend-other {
        background-color: #9ca3af;
    }

    .slot-available {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .slot-available:hover {
        background-color: #d1d5db;
    }

    .slot-selected {
        background-color: #3b82f6;
        color: white;
        font-weight: 500;
    }

    .slot-other {
        background-color: #d1d5db;
        color: #374151;
    }

    .flex {
        display: flex;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .gap-4 {
        gap: 1rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .list-disc {
        list-style-type: disc;
    }

    .list-inside {
        list-style-position: inside;
    }

    .space-y-1>*+* {
        margin-top: 0.25rem;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }
</style>

<div class="schedule-grid-container">
    <!-- Creating Mode Message -->
    @if ($isCreating)
        <div class="schedule-card">
            <h3 class="schedule-title">Configuración de Horarios</h3>
            <div class="schedule-warning">
                <p class="schedule-warning-text">
                    <strong>Importante:</strong> Primero guarda la información del profesor para poder configurar los
                    horarios.
                    La grilla de horarios estará disponible después de crear el profesor.
                </p>
            </div>

            <!-- Preview Schedule Grid (disabled) -->
            <div class="schedule-preview">
                <h4 class="schedule-preview-title">Vista Previa de Grilla de Horarios</h4>
                <div class="schedule-preview-box">
                    <p class="schedule-preview-text">La grilla estará disponible después de guardar el profesor</p>
                </div>
            </div>
        </div>
    @else
        <!-- Branch Selection -->
        <div class="schedule-card">
            <h3 class="schedule-title">Configuración de Horarios</h3>

            <div class="mb-4">
                <label class="schedule-form-label">
                    Selecciona la Sede para Asignar Horarios
                </label>
                <select wire:model.live="selectedBranch" class="schedule-form-select">
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 mb-4">
                <button wire:click="saveSchedules" class="schedule-button-primary">
                    Guardar Horarios
                </button>
                <button wire:click="clearAllSchedules" class="schedule-button-secondary">
                    Limpiar Todo
                </button>
            </div>

            <div class="schedule-instructions">
                <p><strong>Instrucciones:</strong></p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Selecciona una sede del menú desplegable</li>
                    <li>Haz clic en las celdas de la grilla para asignar horarios</li>
                    <li>Vuelve a hacer clic en una celda marcada para quitar el horario</li>
                    <li>Los horarios se guardan automáticamente al presionar "Guardar Horarios"</li>
                </ul>
            </div>
        </div>

        <!-- Schedule Grid -->
        <div class="schedule-table-container">
            <h3 class="schedule-title">Grilla de Horarios (9:00 - 17:00)</h3>

            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="schedule-table-header">
                            Hora
                        </th>
                        @foreach ($days as $index => $day)
                            <th class="schedule-table-header-center">
                                {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timeSlots as $timeSlot)
                        <tr>
                            <td class="schedule-table-cell">
                                {{ $timeSlot }}
                            </td>
                            @foreach ($dayNumbers as $dayIndex => $day)
                                <td class="schedule-table-button-cell">
                                    <button wire:click="toggleTimeSlot('{{ $day }}', '{{ $timeSlot }}')"
                                        class="schedule-table-button"
                                        wire:key="{{ $day }}-{{ $timeSlot }}">
                                        @php
                                            $currentValue = $scheduleData[$day][$timeSlot] ?? null;
                                            if ($currentValue == $selectedBranch) {
                                                echo '<span class="slot-selected">' .
                                                    ($branches[$currentValue] ?? 'Sede') .
                                                    '</span>';
                                            } elseif ($currentValue) {
                                                echo '<span class="slot-other">' .
                                                    ($branches[$currentValue] ?? 'Sede') .
                                                    '</span>';
                                            } else {
                                                echo '<span class="slot-available">Libre</span>';
                                            }
                                        @endphp
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div class="schedule-legend">
            <h4 class="schedule-legend-title">Leyenda</h4>
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="schedule-legend-item">
                    <div class="schedule-legend-color schedule-legend-available"></div>
                    <span>Disponible</span>
                </div>
                @foreach ($branches as $id => $name)
                    <div class="schedule-legend-item">
                        <div
                            class="schedule-legend-color @if ($id == $selectedBranch) schedule-legend-selected @else schedule-legend-other @endif">
                        </div>
                        <span>{{ $name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    window.addEventListener('showNotification', (event) => {
        const {
            message,
            type
        } = event.detail;
        Alpine.store('notification').show(message, type);
    });

    function scheduleGrid() {
        return {
            notification: {
                show: false,
                message: '',
                type: 'info',
                timeout: null
            },

            init() {
                // Initialize Alpine store for notifications
                if (!Alpine.store('notification')) {
                    Alpine.store('notification', {
                        show: (message, type = 'info') => {
                            this.notification = {
                                show: true,
                                message,
                                type,
                                timeout: setTimeout(() => {
                                    this.notification.show = false;
                                }, 3000)
                            };
                        }
                    });
                }
            },

            getSlotClass(day, timeSlot) {
                const scheduleData = @json($scheduleData);
                const selectedBranch = @json($selectedBranch);
                const branchId = scheduleData[day]?.[timeSlot];

                if (branchId == selectedBranch) {
                    return 'slot-selected';
                } else if (branchId) {
                    return 'slot-other';
                } else {
                    return 'slot-available';
                }
            },

            getSlotText(day, timeSlot) {
                const scheduleData = @json($scheduleData);
                const branches = @json($branches);
                const branchId = scheduleData[day]?.[timeSlot];

                if (branchId) {
                    return branches[branchId] || 'Sede';
                } else {
                    return 'Libre';
                }
            }
        }
    }
</script>
