<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_create_product()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'slug' => 'test-product',
                'description' => 'Test description',
                'price' => 100,
                'discount'=> 40,
                'quantity' => 10,
                'image' => \Illuminate\Http\UploadedFile::fake()->image('product.jpg'),
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'created product']);
    }

    public function test_non_admin_cannot_create_product()
    {
        $user = User::where('email', 'john@example.com')->first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'slug' => 'test-product',
                'description' => 'Test description',
                'price' => 100,
                'discount'=> 30,
                'quantity' => 10,
                'image' => \Illuminate\Http\UploadedFile::fake()->image('product.jpg'),
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_product()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $product = Product::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson("/api/products/{$product->id}", [
                'name' => 'Updated Product',
                'slug' => 'updated-product',
                'description' => 'Updated description',
                'price' => 200,
                'discount'=> 10,
                'quantity' => 20,
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'update product']);
    }

    public function test_admin_can_delete_product()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $product = Product::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'deleted product']);
    }
}
?>
