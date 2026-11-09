<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JoinUsController extends Controller
{
    /**
     * Display member applications and community categories.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'member');

        if ($request->filled('status') && in_array($request->query('status'), ['pending', 'approved', 'rejected'])) {
            $query->where('membership_status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $search = '%'.strtolower($request->query('q')).'%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
            });
        }

        $members = $query->latest('id')->paginate(10)->withQueryString();
        $categories = Category::withCount('books')->orderBy('name')->get();

        $totalPending = User::where('role', 'member')->where('membership_status', 'pending')->count();
        $totalApproved = User::where('role', 'member')->where('membership_status', 'approved')->count();
        $totalRejected = User::where('role', 'member')->where('membership_status', 'rejected')->count();

        return view('admin.join_us.index', compact(
            'members',
            'categories',
            'totalPending',
            'totalApproved',
            'totalRejected'
        ));
    }

    /**
     * Approve membership application.
     */
    public function approve(int $id): RedirectResponse
    {
        $user = User::where('role', 'member')->findOrFail($id);
        $user->update(['membership_status' => 'approved']);

        return redirect()->route('admin.join-us.index')
            ->with('success', "Keanggotaan untuk {$user->name} berhasil disetujui!");
    }

    /**
     * Reject membership application.
     */
    public function reject(int $id): RedirectResponse
    {
        $user = User::where('role', 'member')->findOrFail($id);
        $user->update(['membership_status' => 'rejected']);

        return redirect()->route('admin.join-us.index')
            ->with('success', "Pendaftaran untuk {$user->name} telah ditolak.");
    }
}
