<?php

namespace Tests\Unit;

use App\Models\ClassSection;
use App\Models\FormField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Auth;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_works() {
        $this->assertTrue(true);
    }
}
