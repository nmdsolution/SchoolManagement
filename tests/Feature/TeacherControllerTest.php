<?php

namespace Tests\Feature;

use App\Models\Center;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{

    public function test_cannot_access_the_teacher_index_page_without_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/teachers');

        $response->assertRedirect(route('home'));

        $response->assertSessionHasErrors(['message' => trans('no_permission_message')]);
    }

    private function createUserWithPermissions(array $permissions)
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);
        return $user;
    }
}
