<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_home_returns_required_mobile_sections(): void
    {
        $response = $this->getJson('/api/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'banners',
                    'recommended_books',
                    'popular_books',
                    'new_books',
                    'articles',
                    'categories',
                ],
            ]);
    }

    public function test_api_books_search_and_filter(): void
    {
        $response = $this->getJson('/api/books?q=Pulang');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonFragment(['title' => 'Pulang-Pergi']);
        $this->assertIsArray($response->json('data'));
    }

    public function test_api_articles_returns_success_and_list(): void
    {
        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
        $this->assertIsArray($response->json('data'));
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_api_banners_returns_success_and_list(): void
    {
        $response = $this->getJson('/api/banners');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
        $this->assertIsArray($response->json('data'));
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_api_mobile_user_login(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'fadhel@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'token',
                'user' => ['id', 'name', 'email'],
            ]);
    }

    public function test_api_authenticated_user_can_access_shelf(): void
    {
        $user = User::where('email', 'fadhel@gmail.com')->first();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/shelf');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'reading',
                    'wishlist',
                ],
            ]);
    }

    public function test_api_user_can_submit_app_review(): void
    {
        $user = User::where('email', 'fadhel@gmail.com')->first();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/reviews', [
                'rating' => 5,
                'comment' => 'Pengalaman membaca sangat nyaman dan lancar!',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'success']);
    }

    public function test_api_stream_pdf_endpoint(): void
    {
        $book = Book::first();

        $response = $this->get('/api/books/'.$book->id.'/pdf');
        $response->assertStatus(200);
    }

    public function test_api_user_can_update_reading_progress(): void
    {
        $user = User::where('email', 'fadhel@gmail.com')->first();
        $book = Book::first();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/shelf/progress', [
                'book_id' => $book->id,
                'last_page_read' => 10,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'success']);
    }

    public function test_api_books_with_search_and_category_query(): void
    {
        $response = $this->getJson('/api/books?search=Filosofi&category=Psikologi');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals('Filosofi Teras', $response->json('data.0.title'));
        $this->assertArrayHasKey('pages', $response->json('data.0'));
        $this->assertArrayHasKey('size', $response->json('data.0'));
        $this->assertArrayHasKey('pdf_url', $response->json('data.0'));
    }

    public function test_api_book_detail_returns_synopsis_and_pdf_url(): void
    {
        $book = Book::first();

        $response = $this->getJson('/api/books/'.$book->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail buku berhasil diambil',
            ]);
        $this->assertEquals($book->id, $response->json('data.id'));
        $this->assertNotEmpty($response->json('data.synopsis'));
        $this->assertNotEmpty($response->json('data.pdf_url'));
    }

    public function test_api_article_detail_returns_content(): void
    {
        $article = \App\Models\Article::first();

        $response = $this->getJson('/api/articles/'.$article->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail artikel berhasil diambil',
            ]);
        $this->assertEquals($article->id, $response->json('data.id'));
        $this->assertNotEmpty($response->json('data.content'));
    }

    public function test_api_submit_review_with_mobile_fields(): void
    {
        $response = $this->postJson('/api/reviews', [
            'user_name' => 'Fadhel Akbar',
            'user_email' => 'fadhel.akbar@example.com',
            'rating' => 5,
            'book_title' => 'Filosofi Teras',
            'review_text' => 'Buku ini membuka wawasan saya tentang stoikisme.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Ulasan dan rating berhasil dikirim',
            ]);
        $this->assertEquals(5, $response->json('data.rating'));
        $this->assertEquals('Buku ini membuka wawasan saya tentang stoikisme.', $response->json('data.comment'));
    }

    public function test_api_join_us_registration(): void
    {
        $response = $this->postJson('/api/join-us', [
            'name' => 'Rina Novita',
            'email' => 'rina.novita@example.com',
            'phone' => '081234567890',
            'reason' => 'Ingin aktif dalam komunitas membaca remaja.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'Rina Novita',
                    'email' => 'rina.novita@example.com',
                    'phone' => '081234567890',
                    'membership_status' => 'pending',
                ],
            ]);
    }
}
