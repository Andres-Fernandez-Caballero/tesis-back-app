<?php

namespace Tests\Unit\Models;

use App\Models\Therapists\Therapist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapistDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_al_eliminar_un_terapeuta_tambien_se_elimina_su_usuario_asociado()
    {
        $user = User::factory()->create();

        $therapist = Therapist::create([
            'type' => 'MassageTherapist',
            'user_id' => $user->id,
            'nombre' => 'Masajista de Prueba',
            'activo' => true,
        ]);

        $therapist->delete();

        $this->assertModelMissing($therapist);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_al_eliminar_un_terapeuta_sin_usuario_asociado_no_falla()
    {
        $therapist = Therapist::create([
            'type' => 'MassageTherapist',
            'nombre' => 'Masajista Sin Cuenta',
            'activo' => true,
        ]);

        $therapist->delete();

        $this->assertModelMissing($therapist);
    }
}
