<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/admin');

        $dashboardResponse = $this->actingAs($admin)->get('/admin');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('RINGKASAN KOMUNITAS');
    }

    public function test_admin_can_view_books_catalog(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/books');

        $response->assertStatus(200);
        $response->assertSee('Digital Library');
        $response->assertSee('National Geographic');
    }

    public function test_admin_can_create_a_book(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->post('/admin/books', [
            'title' => 'Buku Uji Coba Baru',
            'author' => 'Penulis Uji',
            'category_id' => $category->id,
            'page_count' => 200,
            'rating' => 4.5,
            'is_popular' => 1,
        ]);

        $response->assertRedirect('/admin/books');
        $this->assertDatabaseHas('books', [
            'title' => 'Buku Uji Coba Baru',
            'author' => 'Penulis Uji',
        ]);
    }

    public function test_admin_book_show_redirects_to_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $book = Book::first();

        $response = $this->actingAs($admin)->get("/admin/books/{$book->id}");

        $response->assertRedirect("/admin/books/{$book->id}/edit");
    }

    public function test_admin_article_show_redirects_to_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $article = Article::first();

        $response = $this->actingAs($admin)->get("/admin/articles/{$article->id}");

        $response->assertRedirect("/admin/articles/{$article->id}/edit");
    }

    public function test_admin_can_toggle_review_visibility(): void
    {
        $admin = User::where('role', 'admin')->first();
        $review = Review::first();

        $response = $this->actingAs($admin)->patch("/admin/reviews/{$review->id}/toggle");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_visible' => ! $review->is_visible,
        ]);
    }

    public function test_admin_can_view_user_detail(): void
    {
        $admin = User::where('role', 'admin')->first();
        $member = User::where('role', 'member')->first();

        $response = $this->actingAs($admin)->get("/admin/users/{$member->id}");

        $response->assertStatus(200);
        $response->assertSee($member->name);
        $response->assertSee('Rak Saya');
    }
}
