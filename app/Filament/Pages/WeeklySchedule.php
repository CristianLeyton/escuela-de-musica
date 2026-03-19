<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklySchedule extends Page
{
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $title = 'Clases';

    protected static ?string $navigationLabel = 'Clases';

    protected static ?int $navigationSort = 4;

    protected static string|\UnitEnum|null $navigationGroup = null;

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
        // Establecer fechas por defecto (semana actual)
        $this->start_date = now()->startOfWeek()->format('Y-m-d');
        $this->end_date = now()->endOfWeek()->format('Y-m-d');

        // Debug inicial
        Log::info('WeeklySchedule mount', [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ]);

        $this->loadScheduleData();
    }

    public function loadScheduleData()
    {
        $this->loading = true;

        try {
            $response = Http::withoutVerifying()->get(url('/api/schedules/weekly'), [
                'teacher_id' => $this->teacher_id,
                'student_id' => $this->student_id,
                'instrument_id' => $this->instrument_id,
                'age_group' => $this->age_group,
                'branch_id' => $this->branch_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            $data = $response->json();

            if ($response->successful()) {

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
        Log::info('updatedTeacherId called', ['value' => $this->teacher_id]);
        $this->loadScheduleData();
    }

    public function updatedStudentId()
    {
        $this->loadScheduleData();
    }

    public function updatedInstrumentId()
    {
        $this->loadScheduleData();
    }

    public function updatedAgeGroup()
    {
        $this->loadScheduleData();
    }

    public function updatedBranchId()
    {
        $this->loadScheduleData();
    }

    public function updatedStartDate()
    {
        $this->loadScheduleData();
    }

    public function updatedEndDate()
    {
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
            $response = Http::get(url("http://escuela-de-musica.me/api/schedules/{$classId}"));

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

    public function getTitle(): string
    {
        return 'Horarios Semanales';
    }

    protected function getViewData(): array
    {
        return [];
    }

    public function getView(): string
    {
        return 'filament.pages.weekly-schedule';
    }
}
