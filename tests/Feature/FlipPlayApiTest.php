<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test Happy Path: GET /api/media (Daftar Media)
     */
    public function test_can_list_all_media(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'type', 'genre', 'duration_minutes', 'formatted_duration', 'release_year']
                ]
            ]);
    }

    /**
     * Test Happy Path: GET /api/media?title=Inception (Pencarian Media berdasarkan judul)
     */
    public function test_can_search_media_by_title(): void
    {
        $response = $this->getJson('/api/media?title=Inception');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Inception']);
    }

    /**
     * Test Happy Path: POST /api/media (Tambah Media baru - 201 Created)
     */
    public function test_can_create_new_media(): void
    {
        $payload = [
            'title' => 'The Dark Knight',
            'type' => 'movie',
            'genre' => 'Action',
            'duration_minutes' => 152,
            'release_year' => 2008,
            'director' => 'Christopher Nolan',
            'age_rating' => 'PG-13',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'The Dark Knight')
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('media', ['title' => 'The Dark Knight']);
    }

    /**
     * Test Failure Path: POST /api/media dengan judul kosong (Error 422)
     */
    public function test_create_media_fails_when_title_is_empty(): void
    {
        $payload = [
            'title' => '',
            'type' => 'movie',
            'genre' => 'Action',
            'duration_minutes' => 120,
            'release_year' => 2024,
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test Failure Path: POST /api/media dengan judul duplikat (Error 422)
     */
    public function test_create_media_fails_when_title_is_duplicate(): void
    {
        $payload = [
            'title' => 'Inception', // Sudah ada dari seeder
            'type' => 'movie',
            'genre' => 'Sci-Fi',
            'duration_minutes' => 148,
            'release_year' => 2010,
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test Happy Path: GET /api/media/{id} (Detail media beserta pengguna kolektor - whenLoaded)
     */
    public function test_can_show_media_detail_with_users(): void
    {
        $inception = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->getJson('/api/media/' . $inception->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Inception')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'collected_by_users' => [
                        '*' => ['id', 'name', 'email']
                    ]
                ]
            ]);
    }

    /**
     * Test Happy Path: PUT /api/media/{id} (Update data media - 200 OK)
     */
    public function test_can_update_media(): void
    {
        $media = Media::where('title', 'Viva La Vida')->firstOrFail();

        $response = $this->putJson('/api/media/' . $media->id, [
            'genre' => 'Pop Rock',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.genre', 'Pop Rock');

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'genre' => 'Pop Rock',
        ]);
    }

    /**
     * Test Happy Path: DELETE /api/media/{id} (Hapus media - 200 OK)
     */
    public function test_can_delete_media(): void
    {
        $media = Media::create([
            'title' => 'Temp Media to Delete',
            'type' => 'music',
            'genre' => 'Pop',
            'duration_minutes' => 3,
            'release_year' => 2024,
        ]);

        $response = $this->deleteJson('/api/media/' . $media->id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    /**
     * Test Happy Path: GET /api/users/{id}/collection (Lihat koleksi pengguna)
     */
    public function test_can_get_user_media_collection(): void
    {
        $user = User::where('email', 'himam@example.com')->firstOrFail();

        $response = $this->getJson("/api/users/{$user->id}/collection");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'IQODHUL HIMAM')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'media_collection' => [
                        '*' => ['id', 'title', 'type']
                    ]
                ]
            ]);
    }

    /**
     * Test Happy Path: POST /api/users/{id}/collection (Custom Action: Attach media ke koleksi)
     */
    public function test_can_attach_media_to_user_collection(): void
    {
        $user = User::where('email', 'himam@example.com')->firstOrFail();
        $media = Media::where('title', 'Spirited Away')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $media->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->assertTrue($user->media()->where('media.id', $media->id)->exists());
    }

    /**
     * Test Failure Path: Menambahkan media yang sama dua kali ke koleksi pengguna (Error 422)
     */
    public function test_attach_duplicate_media_to_user_collection_fails_with_422(): void
    {
        $user = User::where('email', 'himam@example.com')->firstOrFail();
        // Inception sudah ada di koleksi user dari seeder
        $inception = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $inception->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['media_id']);
    }

    /**
     * Test Happy Path: DELETE /api/users/{id}/collection/{mediaId} (Custom Action: Detach media dari koleksi)
     */
    public function test_can_detach_media_from_user_collection(): void
    {
        $user = User::where('email', 'himam@example.com')->firstOrFail();
        $inception = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->deleteJson("/api/users/{$user->id}/collection/{$inception->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertFalse($user->media()->where('media.id', $inception->id)->exists());
    }

    /**
     * Test Failure Path: Detach media yang tidak ada di koleksi (Error 404)
     */
    public function test_detach_non_existing_media_returns_404(): void
    {
        $user = User::where('email', 'himam@example.com')->firstOrFail();

        $response = $this->deleteJson("/api/users/{$user->id}/collection/9999");

        $response->assertStatus(404);
    }
}
