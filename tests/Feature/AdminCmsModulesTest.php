<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCmsModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_access_join_us_and_approve_membership(): void
    {
        $admin = User::where('role', 'admin')->first();
        $applicant = User::where('role', 'member')->where('membership_status', 'pending')->first();

        $response = $this->actingAs($admin)->get('/admin/join-us');
        $response->assertStatus(200);
        $response->assertSee('Kelola Join Us');

        $approveResponse = $this->actingAs($admin)->post("/admin/join-us/{$applicant->id}/approve");
        $approveResponse->assertRedirect('/admin/join-us');

        $this->assertDatabaseHas('users', [
            'id' => $applicant->id,
            'membership_status' => 'approved',
        ]);
    }

    public function test_admin_can_view_and_update_identity_content(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/identity');
        $response->assertStatus(200);
        $response->assertSee('Identitas Komunitas');

        $updateResponse = $this->actingAs($admin)->put('/admin/identity', [
            'community_name' => 'Komunitas Literasi Tambun Selatan',
            'app_name' => 'EPerpus Cakrawala Modern',
            'tagline' => 'Perpustakaan digital ramah remaja',
            'contact_email' => 'admin@cakrawala.id',
            'contact_phone' => '08123456789',
            'address' => 'Tambun Selatan, Bekasi',
        ]);

        $updateResponse->assertRedirect('/admin/identity');
        $updateResponse->assertSessionHas('success');
    }

    public function test_admin_can_manage_admin_accounts_and_permissions(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/admins');
        $response->assertStatus(200);
        $response->assertSee('Kelola Admin & Hak Akses');

        // Add new admin
        $storeResponse = $this->actingAs($admin)->post('/admin/admins', [
            'name' => 'Rian Hidayat',
            'email' => 'rian@cakrawala.id',
            'password' => 'secret1234',
            'admin_level' => 'library_admin',
        ]);

        $storeResponse->assertRedirect('/admin/admins');
        $this->assertDatabaseHas('users', [
            'email' => 'rian@cakrawala.id',
            'admin_level' => 'library_admin',
        ]);

        // Prevent self deletion
        $deleteSelfResponse = $this->actingAs($admin)->delete("/admin/admins/{$admin->id}");
        $deleteSelfResponse->assertSessionHas('error');
    }

    public function test_admin_can_reply_to_reader_review(): void
    {
        $admin = User::where('role', 'admin')->first();
        $review = Review::first();

        $replyResponse = $this->actingAs($admin)->post("/admin/reviews/{$review->id}/reply", [
            'reply' => 'Terima kasih atas ulasan yang sangat bermanfaat!',
        ]);

        $replyResponse->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'reply' => 'Terima kasih atas ulasan yang sangat bermanfaat!',
        ]);
    }
}
