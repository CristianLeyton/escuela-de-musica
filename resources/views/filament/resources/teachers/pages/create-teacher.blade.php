<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Main form --}}
        <div class="fi-fo-form-container">
            {{ $this->form }}
        </div>

        {{-- Schedule Grid Section - Show in create mode --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Horarios del Profesor</h3>
            </div>
            <div class="p-6">
                <livewire:teacher-schedule-grid :teacher="null" :is-creating="true" />
            </div>
        </div>
    </div>
</x-filament-panels::page>
