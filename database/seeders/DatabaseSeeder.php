<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Bookmark;
use App\Models\Bookshelf;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@cakrawala.id'],
            [
                'name' => 'Administrator Cakrawala',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'admin_level' => 'super_admin',
                'avatar' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'fdhelfdhel990@gmail.com'],
            [
                'name' => 'Fadhel',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'admin_level' => 'super_admin',
                'avatar' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'pustakawan@cakrawala.id'],
            [
                'name' => 'Dewi Sartika',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'admin_level' => 'library_admin',
                'avatar' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'editor@cakrawala.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'admin_level' => 'article_editor',
                'avatar' => null,
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'fadhel@gmail.com'],
            [
                'name' => 'Fadhel',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'membership_status' => 'approved',
                'avatar' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'sarah@gmail.com'],
            [
                'name' => 'Sarah Amalia',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'membership_status' => 'pending',
                'avatar' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'rizky@gmail.com'],
            [
                'name' => 'Rizky Pratama',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'membership_status' => 'pending',
                'avatar' => null,
            ]
        );

        // 2. Seed Categories
        $categoriesData = [
            [
                'name' => 'Novel Fiksi',
                'slug' => 'novel-fiksi',
                'description' => 'Koleksi novel cerita rekaan dari para sastrawan dan novelis terbaik nusantara dan dunia.',
                'icon' => 'book-open',
            ],
            [
                'name' => 'Psikologi',
                'slug' => 'psikologi',
                'description' => 'Pendekatan sederhana untuk berhenti menghabiskan energi pada hal-hal yang tidak dapat kita kendalikan.',
                'icon' => 'sparkles',
            ],
            [
                'name' => 'Alam',
                'slug' => 'alam',
                'description' => 'Jelajahi keindahan alam, pengetahuan ilmiah, dan beragam kebudayaan melalui cerita visual yang mendalam.',
                'icon' => 'globe',
            ],
            [
                'name' => 'Ilmiah',
                'slug' => 'ilmiah',
                'description' => 'Materialisme, dialektika, logika, dan sains untuk membangun cara berpikir kritis dan rasional.',
                'icon' => 'academic-cap',
            ],
            [
                'name' => 'Anak',
                'slug' => 'anak',
                'description' => 'Petualangan hangat dan penuh pesan moral mendidik untuk anak dan keluarga.',
                'icon' => 'face-smile',
            ],
            [
                'name' => 'Sains',
                'slug' => 'sains',
                'description' => 'Menjelajahi keajaiban ilmu fisika, biologi, astronomi, dan fenomena semesta.',
                'icon' => 'beaker',
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Perkembangan kecerdasan buatan, rekayasa perangkat lunak, dan inovasi masa depan.',
                'icon' => 'cpu-chip',
            ],
            [
                'name' => 'Sejarah',
                'slug' => 'sejarah',
                'description' => 'Menyelami kisah masa lalu para tokoh besar, bangsa, dan peradaban dunia.',
                'icon' => 'clock',
            ],
            [
                'name' => 'Dongeng',
                'slug' => 'dongeng',
                'description' => 'Cerita rakyat, fabel, dan dongeng nusantara yang sarat dengan kearifan lokal.',
                'icon' => 'heart',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Ensure a demo PDF file exists in storage for reading experience
        Storage::disk('public')->makeDirectory('books');
        $samplePdfPath = 'books/sample_pulang_pergi.pdf';
        if (! Storage::disk('public')->exists($samplePdfPath)) {
            // Create a minimal valid PDF containing the Pulang-Pergi demo text
            $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF\n";
            Storage::disk('public')->put($samplePdfPath, $pdfContent);
        }

        // 4. Seed Books
        $booksData = [
            [
                'title' => 'Pulang-Pergi',
                'slug' => 'pulang-pergi',
                'author' => 'Tere Liye',
                'publisher' => 'Sabak Grip Nusantara',
                'publication_year' => 2021,
                'page_count' => 417,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Novel Fiksi']->id,
                'synopsis' => 'Kelanjutan hidup Bujang setelah melepaskan jabatannya sebagai Tauke Besar di Keluarga Tong. Plot utama novel sepanjang 417 halaman ini digerakkan oleh ancaman perjodohan politik, pengkhianatan berdarah, dan pelarian lintas negara yang melibatkan dua tokoh utama dari semesta shadow economy Tere Liye.',
                'cover_image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => true,
                'is_recommended' => true,
                'is_new_collection' => false,
            ],
            [
                'title' => 'Negeri 5 Menara',
                'slug' => 'negeri-5-menara',
                'author' => 'A. Fuadi',
                'publisher' => 'Gramedia Pustaka Utama',
                'publication_year' => 2009,
                'page_count' => 125,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Novel Fiksi']->id,
                'synopsis' => 'Alif Fikri menemukan persahabatan, mimpi, dan kekuatan mantra "Man Jadda Wajada" selama menempuh pendidikan di Pondok Madani. Bersama lima sahabatnya, mereka berjanji di bawah menara untuk menggapai mimpi keliling dunia.',
                'cover_image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => true,
                'is_recommended' => false,
                'is_new_collection' => false,
            ],
            [
                'title' => 'Rantau 1 Muara',
                'slug' => 'rantau-1-muara',
                'author' => 'A. Fuadi',
                'publisher' => 'Gramedia Pustaka Utama',
                'publication_year' => 2013,
                'page_count' => 395,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Novel Fiksi']->id,
                'synopsis' => 'Perjalanan Alif menjemput mimpi di tanah rantau Amerika Serikat, menghadapi tantangan perkuliahan dan dunia kerja baru, serta menemukan tempat muara sejati untuk pulang.',
                'cover_image' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => true,
                'is_recommended' => false,
                'is_new_collection' => false,
            ],
            [
                'title' => 'Filosofi Teras',
                'slug' => 'filosofi-teras',
                'author' => 'Henry Manampiring',
                'publisher' => 'Buku Kompas',
                'publication_year' => 2019,
                'page_count' => 320,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Psikologi']->id,
                'synopsis' => 'Pengantar filsafat Stoa kuno yang sangat relevan dan aplikatif untuk mengatasi kekhawatiran, emosi negatif, dan tantangan kehidupan sehari-hari generasi modern.',
                'cover_image' => 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => true,
                'is_recommended' => true,
                'is_new_collection' => false,
            ],
            [
                'title' => 'Madilog',
                'slug' => 'madilog',
                'author' => 'Tan Malaka',
                'publisher' => 'Narasi',
                'publication_year' => 1943,
                'page_count' => 568,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Ilmiah']->id,
                'synopsis' => 'Materialisme, dialektika, dan logika dalam mahakarya pemikiran Tan Malaka untuk membebaskan masyarakat dari pemikiran mistik menuju cara berpikir ilmiah dan rasional.',
                'cover_image' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => true,
                'is_recommended' => false,
                'is_new_collection' => false,
            ],
            [
                'title' => 'The Let Them Theory',
                'slug' => 'the-let-them-theory',
                'author' => 'Mel Robbins',
                'publisher' => 'Hay House',
                'publication_year' => 2024,
                'page_count' => 336,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Psikologi']->id,
                'synopsis' => 'Pendekatan sederhana dan revolusioner untuk membiarkan orang lain melakukan apa yang ingin mereka lakukan, sehingga Anda dapat membebaskan kedamaian pikiran dan mengendalikan hidup Anda sendiri.',
                'cover_image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => false,
                'is_recommended' => false,
                'is_new_collection' => true,
            ],
            [
                'title' => 'Cantik Itu Luka',
                'slug' => 'cantik-itu-luka',
                'author' => 'Eka Kurniawan',
                'publisher' => 'Gramedia Pustaka Utama',
                'publication_year' => 2002,
                'page_count' => 505,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Novel Fiksi']->id,
                'synopsis' => 'Kisah epik keluarga Dewi Ayu yang membentang lintas generasi dengan sejarah kolonialisme, romansa, dendam, dan luka yang saling bertaut dalam keajaiban realisme magis.',
                'cover_image' => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => false,
                'is_recommended' => false,
                'is_new_collection' => true,
            ],
            [
                'title' => 'Laskar Pelangi',
                'slug' => 'laskar-pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2005,
                'page_count' => 529,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Novel Fiksi']->id,
                'synopsis' => 'Perjuangan sepuluh anak Belitung di SD Muhammadiyah Gantong untuk terus belajar, bermimpi besar, dan bertahan di tengah segala keterbatasan ekonomi.',
                'cover_image' => 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => false,
                'is_recommended' => true,
                'is_new_collection' => false,
            ],
            [
                'title' => 'Briana dan Bottomwise',
                'slug' => 'briana-dan-bottomwise',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2022,
                'page_count' => 380,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Novel Fiksi']->id,
                'synopsis' => 'Petualangan penuh kejutan, kehangatan, dan humor khas Andrea Hirata yang mengisahkan dinamika kehidupan seorang guru dan muridnya di pelosok negeri.',
                'cover_image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => false,
                'is_recommended' => false,
                'is_new_collection' => false,
            ],
            [
                'title' => 'Pukat',
                'slug' => 'pukat',
                'author' => 'Tere Liye',
                'publisher' => 'Republika',
                'publication_year' => 2010,
                'page_count' => 352,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Anak']->id,
                'synopsis' => 'Petualangan Pukat, si anak paling pintar dalam keluarga sederhana di pedalaman Sumatra yang penuh dengan keceriaan anak-anak dan hikmah kehidupan.',
                'cover_image' => 'https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => false,
                'is_recommended' => false,
                'is_new_collection' => true,
            ],
            [
                'title' => 'National Geographic: Keajaiban Alam Nusantara',
                'slug' => 'national-geographic-keajaiban-alam',
                'author' => 'Andi Rahmat',
                'publisher' => 'National Geographic Indonesia',
                'publication_year' => 2023,
                'page_count' => 124,
                'file_size' => '25.6 MB',
                'rating' => 4.3,
                'category_id' => $categories['Alam']->id,
                'synopsis' => 'Jelajahi keindahan alam, pengetahuan ilmiah, dan beragam kebudayaan melalui cerita visual serta dokumentasi fotografi yang mendalam.',
                'cover_image' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=600&q=80',
                'file_path' => $samplePdfPath,
                'is_popular' => true,
                'is_recommended' => false,
                'is_new_collection' => false,
            ],
        ];

        $books = [];
        foreach ($booksData as $b) {
            $books[$b['slug']] = Book::updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 5. Seed Articles & News
        $articlesData = [
            [
                'title' => 'Program Literasi Digital Cakrawala Resmi Dibuka',
                'slug' => 'program-literasi-digital-cakrawala-resmi-dibuka',
                'type' => 'kabar',
                'author' => 'Andi Rahmat',
                'image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Program baru untuk membantu anggota mengakses koleksi dan layanan digital perpustakaan secara gratis.',
                'content' => "Perpustakaan Cakrawala resmi membuka Program Literasi Digital untuk seluruh anggota. Program ini menghadirkan kelas pencarian informasi, pengenalan koleksi elektronik, dan pendampingan penggunaan layanan perpustakaan digital.\n\nKegiatan berlangsung setiap akhir pekan dan dapat diikuti tanpa biaya tambahan oleh seluruh anggota aktif yang telah mendaftar melalui aplikasi Cakrawala.",
                'published_at' => '2025-05-12',
            ],
            [
                'title' => 'Jadwal Layanan dan Peminjaman Bulan Ini',
                'slug' => 'jadwal-layanan-dan-peminjaman-bulan-ini',
                'type' => 'kabar',
                'author' => 'Andi Rahmat',
                'image' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Simak pembaruan jam layanan dan ketentuan peminjaman koleksi fisik maupun digital.',
                'content' => "Mulai bulan ini, layanan Perpustakaan Cakrawala dibuka lebih panjang pada hari kerja. Anggota dapat melakukan peminjaman, pengembalian, dan konsultasi pustakawan sesuai jadwal terbaru.\n\nPengingat jatuh tempo akan dikirim langsung melalui aplikasi agar proses membaca tetap nyaman tanpa kendala keterlambatan.",
                'published_at' => '2025-05-08',
            ],
            [
                'title' => 'Koleksi Baru Pilihan Pustakawan',
                'slug' => 'koleksi-baru-pilihan-pustakawan',
                'type' => 'kabar',
                'author' => 'Andi Rahmat',
                'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Beragam judul pilihan kini tersedia dan dapat langsung ditambahkan ke rak bacaan Anda.',
                'content' => "Tim pustakawan telah menambahkan sejumlah koleksi baru dari kategori fiksi, psikologi, ilmu pengetahuan, dan bacaan anak.\n\nSeluruh koleksi telah melalui proses kurasi ketat dan kini dapat ditemukan melalui pencarian maupun bagian rekomendasi di Beranda aplikasi.",
                'published_at' => '2025-05-03',
            ],
            [
                'title' => 'Bedah Buku Bersama Komunitas Cakrawala',
                'slug' => 'bedah-buku-bersama-komunitas-cakrawala',
                'type' => 'kabar',
                'author' => 'Andi Rahmat',
                'image' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Bergabung dalam diskusi buku dan berbagi pengalaman membaca bersama para penggiat literasi.',
                'content' => "Komunitas pembaca Cakrawala kembali mengadakan kegiatan bedah buku bulanan. Acara menghadirkan pustakawan dan pembaca untuk membahas gagasan utama buku pilihan.\n\nPendaftaran dibuka melalui meja informasi perpustakaan atau secara online melalui fitur tiket aplikasi dengan kuota terbatas.",
                'published_at' => '2025-04-28',
            ],
        ];

        foreach ($articlesData as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 6. Seed Banners
        $bannersData = [
            [
                'title' => 'Agenda Literasi Cakrawala',
                'subtitle' => 'Bedah buku dan kelas membaca bersama',
                'image' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=1000&q=80',
                'action_type' => 'article',
                'action_target' => 'bedah-buku-bersama-komunitas-cakrawala',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Koleksi Baru Bulan Ini',
                'subtitle' => 'Temukan bacaan pilihan pustakawan',
                'image' => 'https://images.unsplash.com/photo-1507842229450-782124d4ce92?auto=format&fit=crop&w=1000&q=80',
                'action_type' => 'url',
                'action_target' => '/books?filter=new',
                'order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($bannersData as $ban) {
            Banner::updateOrCreate(['title' => $ban['title']], $ban);
        }

        // 7. Seed Member's Bookshelf (Fadhel)
        if (isset($books['pulang-pergi'])) {
            Bookshelf::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'book_id' => $books['pulang-pergi']->id,
                    'type' => 'reading',
                ],
                [
                    'progress_percentage' => 5,
                    'last_page_read' => 2,
                    'last_read_at' => now(),
                ]
            );

            // Add Bookmark for Pulang-Pergi
            Bookmark::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'book_id' => $books['pulang-pergi']->id,
                    'page_number' => 2,
                ],
                [
                    'note' => 'Pembukaan bab pengkhianatan di Rusia',
                ]
            );
        }

        if (isset($books['negeri-5-menara'])) {
            Bookshelf::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'book_id' => $books['negeri-5-menara']->id,
                    'type' => 'wishlist',
                ],
                [
                    'progress_percentage' => 0,
                    'last_page_read' => 1,
                ]
            );
        }

        if (isset($books['rantau-1-muara'])) {
            Bookshelf::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'book_id' => $books['rantau-1-muara']->id,
                    'type' => 'wishlist',
                ],
                [
                    'progress_percentage' => 0,
                    'last_page_read' => 1,
                ]
            );
        }

        // 8. Seed Reviews
        Review::updateOrCreate(
            [
                'user_id' => $member->id,
                'book_id' => null, // General app review as seen on the profile mockup
            ],
            [
                'rating' => 5,
                'comment' => 'EPerpus Cakrawala sangat membantu dan membaca buku terasa nyaman sekali! Pengalaman membaca digital jadi luar biasa.',
                'is_visible' => true,
            ]
        );
    }
}
