<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class WeeklyScheduleGrid extends Component
{
    use WithPagination;

    // Filtros
    public $teacher_id = null;
    public $student_id = null;
    public $instrument_id = null;
    public $age_group = null;
    public $branch_id = null;
    public $start_date = null;
    public $end_date = null;

    // Datos del grid
    public $gridData = [];
    public $availableHours = [];
    public $daysOfWeek = [];
    public $dateRange = [];
    public $filters = [];

    // Estado
    public $loading = false;
    public $selectedClass = null;
    public $showClassModal = false;

    // Opciones para filtros
    public $teachers = [];
    public $students = [];
    public $instruments = [];
    public $branches = [];
    public $ageGroups = [];

    protected $queryString = [
        'teacher_id',
        'student_id',
        'instrument_id',
        'age_group',
        'branch_id',
        'start_date',
        'end_date',
    ];

    public function mount()
    {
        $this->loadScheduleData();
    }

    public function loadScheduleData()
    {
        $this->loading = true;

        try {
            $response = Http::get(url('/api/schedules/weekly'), [
                'teacher_id' => $this->teacher_id,
                'student_id' => $this->student_id,
                'instrument_id' => $this->instrument_id,
                'age_group' => $this->age_group,
                'branch_id' => $this->branch_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->gridData = $data['data']['grid'] ?? [];
                $this->availableHours = $data['data']['available_hours'] ?? [];
                $this->daysOfWeek = $data['data']['days_of_week'] ?? [];
                $this->dateRange = $data['data']['date_range'] ?? [];

                // Cargar opciones para filtros
                $this->filters = $data['filters'] ?? [];
                $this->teachers = $this->filters['teachers'] ?? [];
                $this->students = $this->filters['students'] ?? [];
                $this->instruments = $this->filters['instruments'] ?? [];
                $this->branches = $this->filters['branches'] ?? [];
                $this->ageGroups = $this->filters['age_groups'] ?? [];
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Error al cargar los datos del horario'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading schedule data: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error de conexión al cargar los datos'
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function updatedTeacherId()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedStudentId()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedInstrumentId()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedAgeGroup()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedBranchId()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
        $this->loadScheduleData();
    }

    public function clearFilters()
    {
        $this->reset([
            'teacher_id',
            'student_id',
            'instrument_id',
            'age_group',
            'branch_id',
            'start_date',
            'end_date',
        ]);

        $this->loadScheduleData();
    }

    public function showClassDetails($classId)
    {
        try {
            $response = Http::get(url("/api/schedules/{$classId}"));

            if ($response->successful()) {
                $this->selectedClass = $response->json('data');
                $this->showClassModal = true;
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Error al cargar los detalles de la clase'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading class details: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error de conexión al cargar los detalles'
            ]);
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
        return view('livewire.weekly-schedule-grid');
    }
}
