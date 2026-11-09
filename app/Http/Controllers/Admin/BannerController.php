<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index(): View
    {
        $banners = Banner::orderBy('order', 'asc')->paginate(10);

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'action_type' => ['nullable', 'string', 'in:book,article,url,none'],
            'action_target' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('banners', 'public');
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'action_type' => $validated['action_type'] ?? 'none',
            'action_target' => $validated['action_target'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promosi beranda baru berhasil ditambahkan!');
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'action_type' => ['nullable', 'string', 'in:book,article,url,none'],
            'action_target' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image_file')) {
            if ($banner->image && ! Str::startsWith($banner->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($banner->image);
            }
            $imagePath = $request->file('image_file')->store('banners', 'public');
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'action_type' => $validated['action_type'] ?? ($banner->action_type ?? 'none'),
            'action_target' => $validated['action_target'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promosi berhasil diperbarui!');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image && ! Str::startsWith($banner->image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promosi berhasil dihapus.');
    }
}
