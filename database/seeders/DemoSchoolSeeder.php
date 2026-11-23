<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSchoolSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new \RuntimeException('Demo accounts may only be seeded in the local environment.');
        }

        DB::transaction(function () {
            $this->demoUser('admin', 'Jordan Santos');
            $teacher = $this->demoUser('teacher', 'Casey Reyes')->teacher()->firstOrCreate();

            Subject::firstOrCreate(['code' => 'IS101'], ['name' => 'Introduction to Information Systems', 'teacher_id' => $teacher->id]);
        });
    }

    private function demoUser(string $role, string $name): User
    {
        $email = $role.'@sis.example.test';
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            if ($user->role !== $role) {
                throw new \RuntimeException('A demo email belongs to a different role.');
            }

            return $user;
        }

        $user = new User(['name' => $name, 'email' => $email, 'password' => 'LocalDemo-2026!']);
        $user->role = $role;
        $user->save();

        return $user;
    }
}
