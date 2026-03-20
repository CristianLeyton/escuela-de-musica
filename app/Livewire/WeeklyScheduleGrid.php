<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Instrument;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeeklyScheduleGrid extends Component
{
    use WithPagination;

    public string $instrument_id = '';

    public string $branch_id = '';

    public string $age = '';

    public array $gridData = [];

    public array $availableHours = [];

    public array $daysOfWeek = [];

    public array $instruments = [];

    public array $branches = [];

    public bool $loading = false;

    public bool $showClassModal = false;

    /** @var array<int, array<string, mixed>> */
    public array $modalOfferings = [];

    public string $modalInstrument = '';

    public string $modalDay = '';

    public string $modalHour = '';

    protected $queryString = [
        'instrument_id' => ['except' => ''],
        'branch_id' => ['except' => ''],
        'age' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->applyDefaultFilters();
        $this->loadScheduleData();
    }

    protected function applyDefaultFilters(): void
    {
        $centroId = Branch::query()->where('is_active', true)->where('name', 'Centro')->value('id');
        $this->branch_id = $centroId ? (string) $centroId : '';

        $firstInstrumentId = Instrument::query()->where('is_active', true)->orderBy('name')->value('id');
        $this->instrument_id = $firstInstrumentId ? (string) $firstInstrumentId : '';
    }

    public function loadScheduleData(): void
    {
        $this->loading = true;

        try {
            $response = Http::withoutVerifying()->get(url('/api/schedules/weekly'), [
                'instrument_id' => $this->instrument_id !== '' ? $this->instrument_id : null,
                'branch_id' => $this->branch_id !== '' ? $this->branch_id : null,
                'age' => $this->age !== '' ? $this->age : null,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->gridData = $data['data']['grid'] ?? [];
                $this->availableHours = $data['data']['available_hours'] ?? [];
                $this->daysOfWeek = $data['data']['days_of_week'] ?? [];
                $this->instruments = $data['filters']['instruments'] ?? [];
                $this->branches = $data['filters']['branches'] ?? [];
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Error al cargar los datos del horario',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading schedule data: '.$e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error de conexión al cargar los datos',
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function updatedInstrumentId(): void
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedBranchId(): void
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedAge(): void
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function clearFilters(): void
    {
        $this->age = '';
        $this->applyDefaultFilters();
        $this->loadScheduleData();
    }

    /**
     * @param  array{instrument: string, day: string, hour: string, offerings: array<int, array<string, mixed>>}  $payload
     */
    public function openCellModal(array $payload): void
    {
        $this->modalInstrument = (string) ($payload['instrument'] ?? '');
        $this->modalDay = (string) ($payload['day'] ?? '');
        $this->modalHour = (string) ($payload['hour'] ?? '');
        $this->modalOfferings = array_values($payload['offerings'] ?? []);
        $this->showClassModal = true;
    }

    public function closeModal(): void
    {
        $this->showClassModal = false;
        $this->modalOfferings = [];
        $this->modalInstrument = '';
        $this->modalDay = '';
        $this->modalHour = '';
    }

    public function getClassesForCell($day, $hour): array
    {
        return $this->gridData[$day][$hour] ?? [];
    }

    public function render()
    {
        return view('livewire.schedule-filter');
    }
}
