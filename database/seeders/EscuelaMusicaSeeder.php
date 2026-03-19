<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Instrument;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EscuelaMusicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $roles = ['admin', 'teacher', 'student'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Crear sedes
        $sedes = [
            ['name' => 'Sur', 'address' => 'Av. Principal 123', 'phone' => '123-456-7890', 'email' => 'sur@escuela.com'],
            ['name' => 'Norte', 'address' => 'Calle Secundaria 456', 'phone' => '098-765-4321', 'email' => 'norte@escuela.com'],
            ['name' => 'Centro', 'address' => 'Plaza Central 789', 'phone' => '555-123-4567', 'email' => 'centro@escuela.com'],
        ];

        foreach ($sedes as $sede) {
            Branch::firstOrCreate(['name' => $sede['name']], $sede);
        }

        // Crear instrumentos
        $instrumentos = [
            ['name' => 'Guitarra', 'description' => 'Guitarra acústica y eléctrica', 'category' => 'cuerda'],
            ['name' => 'Piano', 'description' => 'Piano digital y acústico', 'category' => 'teclado'],
            ['name' => 'Violín', 'description' => 'Violín clásico', 'category' => 'cuerda'],
            ['name' => 'Batería', 'description' => 'Batería acústica', 'category' => 'percusión'],
            ['name' => 'Saxofón', 'description' => 'Saxofón tenor', 'category' => 'viento'],
            ['name' => 'Flauta', 'description' => 'Flauta traversa', 'category' => 'viento'],
        ];

        foreach ($instrumentos as $instrumento) {
            Instrument::firstOrCreate(['name' => $instrumento['name']], $instrumento);
        }

        // Crear aulas para cada sede
        $branches = Branch::all();
        foreach ($branches as $branch) {
            for ($i = 1; $i <= 3; $i++) {
                Classroom::firstOrCreate([
                    'branch_id' => $branch->id,
                    'name' => "Aula {$i}"
                ], [
                    'capacity' => 5,
                    'equipment' => 'Piano, sillas, atriles',
                    'is_active' => true
                ]);
            }
        }

        // Crear usuario admin
        $admin = User::firstOrCreate(['email' => 'admin@escuela.com'], [
            'name' => 'Administrador',
            'lastname' => 'Sistema',
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
        $admin->assignRole('admin');

        // Crear usuarios teacheres
        $teachersData = [
            ['name' => 'Carlos', 'lastname' => 'Rodríguez', 'email' => 'carlos@escuela.com', 'specialization' => 'Guitarra', 'username' => 'carlos_rod'],
            ['name' => 'Ana', 'lastname' => 'Martínez', 'email' => 'ana@escuela.com', 'specialization' => 'Piano', 'username' => 'ana_mar'],
            ['name' => 'Luis', 'lastname' => 'García', 'email' => 'luis@escuela.com', 'specialization' => 'Violín', 'username' => 'luis_gar'],
        ];

        foreach ($teachersData as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'lastname' => $data['lastname'],
                'username' => $data['username'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('teacher');

            Teacher::firstOrCreate(['user_id' => $user->id], [
                'specialization' => $data['specialization'],
                'experience_years' => rand(3, 15),
                'bio' => "teacher de {$data['specialization']} con amplia experiencia",
                'is_active' => true
            ]);
        }

        // Crear usuarios students
        $studentsData = [
            ['name' => 'María', 'lastname' => 'López', 'email' => 'maria@escuela.com', 'age_group' => 'adolescente', 'username' => 'maria_lop'],
            ['name' => 'Juan', 'lastname' => 'Pérez', 'email' => 'juan@escuela.com', 'age_group' => 'niño', 'username' => 'juan_per'],
            ['name' => 'Laura', 'lastname' => 'Sánchez', 'email' => 'laura@escuela.com', 'age_group' => 'adulto', 'username' => 'laura_san'],
            ['name' => 'Pedro', 'lastname' => 'Díaz', 'email' => 'pedro@escuela.com', 'age_group' => 'adolescente', 'username' => 'pedro_dia'],
            ['name' => 'Sofía', 'lastname' => 'Fernández', 'email' => 'sofia@escuela.com', 'age_group' => 'niño', 'username' => 'sofia_fer'],
        ];

        foreach ($studentsData as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'lastname' => $data['lastname'],
                'username' => $data['username'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('student');

            Student::firstOrCreate(['user_id' => $user->id], [
                'birth_date' => now()->subYears(rand(8, 40)),
                'age_group' => $data['age_group'],
                'phone' => '555-' . rand(1000, 9999),
                'emergency_contact' => 'Contacto de emergencia',
                'is_active' => true
            ]);
        }

        // Asignar instrumentos a teacheres
        $teachers = Teacher::all();
        $instruments = Instrument::all();

        foreach ($teachers as $teacher) {
            // Cada teacher enseña 1-3 instrumentos
            $teacherInstruments = $instruments->random(rand(1, 3));
            $teacher->instruments()->attach($teacherInstruments->pluck('id'));
        }

        $this->command->info('✅ Datos iniciales de la escuela de música creados exitosamente');
        $this->command->info('📧 Usuarios de prueba:');
        $this->command->info('   Admin: admin@escuela.com / password');
        $this->command->info('   Teachers: carlos@escuela.com, ana@escuela.com, luis@escuela.com / password');
        $this->command->info('   Students: maria@escuela.com, juan@escuela.com, laura@escuela.com / password');
    }
}
