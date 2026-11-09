<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class IdentityController extends Controller
{
    /**
     * Path to identity settings JSON storage file.
     */
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/identity_settings.json');
    }

    /**
     * Show identity & community settings.
     */
    public function index(): View
    {
        $settings = $this->getSettings();

        return view('admin.identity.index', compact('settings'));
    }

    /**
     * Update identity & community settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'community_name' => 'required|string|max:255',
            'app_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'about' => 'nullable|string',
            'terms' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $directory = dirname($this->storagePath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($this->storagePath, json_encode($validated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.identity.index')
            ->with('success', 'Informasi identitas komunitas dan aplikasi berhasil diperbarui!');
    }

    /**
     * Retrieve settings with default fallback values.
     *
     * @return array<string, string|null>
     */
    protected function getSettings(): array
    {
        $defaults = [
            'community_name' => 'Komunitas Literasi Remaja • Tambun Selatan',
            'app_name' => 'EPerpus Cakrawala',
            'tagline' => 'Tempat di mana kamu bisa mengoptimalkan kenyamanan membaca dalam satu aplikasi',
            'vision' => "Membangun generasi muda yang cerdas, kritis, dan berwawasan luas melalui kecintaan membaca dan literasi digital yang inklusif.",
            'mission' => "1. Menyediakan akses mudah ke koleksi bacaan digital berkualitas.\n2. Mendorong diskusi dan penulisan artikel literasi di kalangan remaja.\n3. Menjalin kemitraan edukatif dengan sekolah dan komunitas literasi lokal.",
            'about' => "Cakrawala Digital Library adalah platform perpustakaan digital dan wadah komunitas literasi remaja yang berpusat di Tambun Selatan, Bekasi. Kami berdedikasi untuk memudahkan anak muda mengakses bahan bacaan berkualitas kapan pun dan di mana pun.",
            'terms' => "1. Pengguna wajib menjaga etika dalam memberikan ulasan buku.\n2. Konten e-book hanya untuk kepentingan membaca pribadi dan tidak diperkenankan disebarluaskan secara komersial.\n3. Keanggotaan terbuka untuk seluruh pegiat literasi dan pelajar.",
            'privacy_policy' => "Data pribadi pengguna (nama & email) digunakan hanya untuk keperluan autentikasi akun dan peningkatan mutu layanan aplikasi EPerpus Cakrawala.",
            'contact_email' => 'literasi@cakrawala.id',
            'contact_phone' => '+62 812-3456-7890',
            'address' => 'Tambun Selatan, Kabupaten Bekasi, Jawa Barat',
        ];

        if (File::exists($this->storagePath)) {
            $json = json_decode(File::get($this->storagePath), true);
            if (is_array($json)) {
                return array_merge($defaults, $json);
            }
        }

        return $defaults;
    }
}
