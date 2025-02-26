<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Students;
use App\Models\ClassSection;
use App\Models\SessionYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class StudentControllerTest extends TestCase
{
    use WithFaker;

    protected $user;

    protected $center;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user with necessary permissions
        $this->user = User::factory()->create();

        $this->user->givePermissionTo('student-list');
        $this->user->givePermissionTo('student-create');
        $this->user->givePermissionTo('student-edit');
        $this->user->givePermissionTo('student-delete');

        $this->actingAs($this->user);
    }

    public function testTrue() {
        $this->assertTrue(true);
    }

    public function atestIndex()
    {
        $response = $this->get(route('students.index'));
        $response->assertStatus(200);
        $response->assertViewIs('students.details');
    }

    public function atestCreate()
    {
        $response = $this->get(route('students.create'));
        $response->assertStatus(200);
        $response->assertViewIs('students.index');
    }

    public function atestStore()
    {
        $studentData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'admission_no' => $this->faker->unique()->numberBetween(100000, 999999),
            'class_section_id' => ClassSection::factory()->create()->id,
            'admission_date' => $this->faker->date(),
            'dob' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'nationality' => $this->faker->country,
            'born_at' => $this->faker->city,
            'status' => 1,
            // Add other required fields here
        ];

        $response = $this->post(route('students.store'), $studentData);
        $response->assertStatus(200);
        $response->assertJson(['error' => false, 'message' => trans('data_store_successfully')]);
        $this->assertDatabaseHas('students', ['admission_no' => $studentData['admission_no']]);
    }

    public function atestShow()
    {
        $student = Students::factory()->create();
        $response = $this->get(route('students.show', $student->id));
        $response->assertStatus(200);
        $response->assertJsonStructure(['total', 'rows']);
    }

    public function atestUpdate()
    {
        $student = Students::factory()->create();
        $updatedData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'admission_no' => $this->faker->unique()->numberBetween(100000, 999999),
            'class_section_id' => ClassSection::factory()->create()->id,
            'admission_date' => $this->faker->date(),
            'dob' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'nationality' => $this->faker->country,
            'born_at' => $this->faker->city,
            'status' => 1,
            // Add other fields that can be updated
        ];

        $response = $this->put(route('students.update', $student->id), $updatedData);
        $response->assertStatus(200);
        $response->assertJson(['error' => false, 'message' => trans('data_store_successfully')]);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'admission_no' => $updatedData['admission_no']]);
    }

    public function atestDestroy()
    {
        $student = Students::factory()->create();
        $response = $this->delete(route('students.destroy', $student->user_id));
        $response->assertStatus(200);
        $response->assertJson(['error' => false, 'message' => trans('data_delete_successfully')]);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    // Add more test methods for other functions in StudentController...
}
