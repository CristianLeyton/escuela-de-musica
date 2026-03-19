<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScheduleFilter extends Component
{
    // Filtros
    public $teacher_id = '';
    public $student_id = '';
    public $instrument_id = '';
    public $age_group = '';
    public $branch_id = '';
    public $start_date = '';
    public $end_date = '';

    // Datos
    public $gridData = [];
    public $availableHours = [];
    public $daysOfWeek = [];
    public $dateRange = [];
    public $teachers = [];
    public $students = [];
    public $instruments = [];
    public $branches = [];
    public $ageGroups = [];
    public $loading = false;
    public $selectedClass = null;
    public $showClassModal = false;

    protected $queryString = [
        'teacher_id' => ['except' => ''],
        'student_id' => ['except' => ''],
        'instrument_id' => ['except' => ''],
        'age_group' => ['except' => ''],
        'branch_id' => ['except' => ''],
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
    ];

    public function mount()
    {
        $this->start_date = now()->startOfWeek()->format('Y-m-d');
        $this->end_date = now()->endOfWeek()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->loading = true;

        try {
            $response = Http::withoutVerifying()->get(url('/api/schedules/weekly'), [
                'teacher_id' => $this->teacher_id ?: null,
                'student_id' => $this->student_id ?: null,
                'instrument_id' => $this->instrument_id ?: null,
                'age_group' => $this->age_group ?: null,
                'branch_id' => $this->branch_id ?: null,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->gridData = $data['data']['grid'] ?? [];
                $this->availableHours = $data['data']['available_hours'] ?? [];
                $this->daysOfWeek = $data['data']['days_of_week'] ?? [];
                $this->dateRange = $data['data']['date_range'] ?? [];
                $this->teachers = $data['filters']['teachers'] ?? [];
                $this->students = $data['filters']['students'] ?? [];
                $this->instruments = $data['filters']['instruments'] ?? [];
                $this->branches = $data['filters']['branches'] ?? [];
                $this->ageGroups = $data['filters']['age_groups'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading schedule data: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['teacher_id', 'student_id', 'instrument_id', 'age_group', 'branch_id', 'start_date', 'end_date'])) {
            $this->loadData();
        }
    }

    public function clearFilters()
    {
        $this->reset(['teacher_id', 'student_id', 'instrument_id', 'age_group', 'branch_id']);
        $this->loadData();
    }

    public function showClassDetails($classId)
    {
        try {
            $response = Http::withoutVerifying()->get(url("/api/schedules/{$classId}"));
            if ($response->successful()) {
                $this->selectedClass = $response->json('data');
                $this->showClassModal = true;
            }
        } catch (\Exception $e) {
            Log::error('Error loading class details: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showClassModal = false;
        $this->selectedClass = null;
    }

    public function getClassesForCell($day, $hour)
    {
        return $this->gridData[$day][$hour] ?? [];
    }

    public function render()
    {
        return view('livewire.schedule-filter');
    }
}
