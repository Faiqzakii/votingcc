<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;

class UserImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_import_fails_without_email_handling()
    {
        // Define CSV content without email (headers: name, username, password)
        $csvContent = "name,username,password\nJohn Doe,johndoe,password123";
        
        // Create a temporary file
        $file = UploadedFile::fake()->createWithContent('users.csv', $csvContent);

        // Perform the POST request to import
        // Assuming route name is 'users.import.store' from the view analysis
        $response = $this->post(route('users.import.store'), [
            'file' => $file,
        ]);

        // Expectation:
        // After fix, the user should be created with the generated email.
        
        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
        ]);
        
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
    }
}
