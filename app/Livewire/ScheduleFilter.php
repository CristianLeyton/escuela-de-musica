<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Instrument;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScheduleFilter extends Component
{
    public string $instrument_id = '';

    public string $branch_id = '';

    public string $age = '';

    public array $gridData = [];

    public array $availableHours = [];

    public array $daysOfWeek = [];

    public array $dateRange = [];

    public array $instruments = [];

    public array $branches = [];

    public bool $loading = false;

    public bool $showClassModal = false;

    /** @var array<int, array{id: int, teacher: string, branch: string, enrolled_count: int}> */
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
        $this->loadData();
    }

    protected function applyDefaultFilters(): void
    {
        $centroId = Branch::query()->where('is_active', true)->where('name', 'Centro')->value('id');
        $this->branch_id = $centroId ? (string) $centroId : '';

        $firstInstrumentId = Instrument::query()->where('is_active', true)->orderBy('name')->value('id');
        $this->instrument_id = $firstInstrumentId ? (string) $firstInstrumentId : '';
    }

    public function loadData(): void
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
                $this->dateRange = $data['data']['date_range'] ?? [];
                $this->instruments = $data['filters']['instruments'] ?? [];
                $this->branches = $data['filters']['branches'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading schedule data: '.$e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function updated($property): void
    {
        if (in_array($property, ['instrument_id', 'branch_id', 'age'], true)) {
            $this->loadData();
        }
    }

    public function clearFilters(): void
    {
        $this->age = '';
        $this->applyDefaultFilters();
        $this->loadData();
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
