<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\BorrowingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    // WEB endpoints (mengembalikan view)
    public function indexAssets()
    {
        return view('borrowings.assets');
    }

    public function create(Request $request)
    {
        $assets = Asset::all();
        $selectedAssetId = $request->query('asset_id');
        return view('borrowings.create', compact('assets', 'selectedAssetId'));
    }

    public function indexMyRequests()
    {
        return view('borrowings.my_requests');
    }

    public function indexAdminRequests()
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'lead') {
            abort(403, 'Unauthorized action.');
        }
        return view('borrowings.admin');
    }

    // API endpoint untuk mendapatkan daftar aset (dipakai nanti di frontend)
    public function getAssets()
    {
        $assets = Asset::all();
        return response()->json($assets);
    }

    // API endpoint untuk mendapatkan riwayat peminjaman user
    public function getMyRequests()
    {
        $requests = BorrowingRequest::with(['asset', 'approver'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($requests);
    }

    // API endpoint untuk admin (mendapatkan semua request)
    public function getAllRequests()
    {
        $requests = BorrowingRequest::with(['asset', 'user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($requests);
    }

    // API untuk membuat request baru
    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|string|exists:assets,id',
            'borrow_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:borrow_date',
            'purpose' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $borrowRequest = BorrowingRequest::create([
            'asset_id' => $validated['asset_id'],
            'user_id' => Auth::id(),
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request created successfully', 'data' => $borrowRequest]);
    }

    // API untuk update status (admin/TU)
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,returned,overdue',
        ]);

        $borrowRequest = BorrowingRequest::findOrFail($id);
        $borrowRequest->status = $validated['status'];
        
        if (in_array($validated['status'], ['approved', 'rejected'])) {
            $borrowRequest->approved_by = Auth::id();
        }

        if ($validated['status'] === 'returned') {
            $borrowRequest->actual_return_date = now();
        }

        $borrowRequest->save();

        // Kirim Notifikasi ke User
        $statusIndo = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Sudah Dikembalikan',
            'overdue'  => 'Terlambat',
            'pending'  => 'Menunggu'
        ];

        \App\Models\Notification::create([
            'user_id' => $borrowRequest->user_id,
            'title' => 'Update Status Peminjaman',
            'message' => 'Peminjaman ' . ($borrowRequest->asset->name ?? 'Aset') . ' Anda kini berstatus: ' . ($statusIndo[$validated['status']] ?? $validated['status']),
            'link' => '/borrowings',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Status updated successfully', 'data' => $borrowRequest]);
    }

    // API untuk menghapus request (hanya jika masih pending / oleh user)
    public function destroy($id)
    {
        $borrowRequest = BorrowingRequest::findOrFail($id);
        
        if ($borrowRequest->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($borrowRequest->status !== 'pending') {
            return response()->json(['error' => 'Cannot delete processed request'], 400);
        }

        $borrowRequest->delete();

        return response()->json(['message' => 'Request deleted successfully']);
    }

    // --- ADMIN ASSET MANAGEMENT CRUD ---

    public function storeAsset(Request $request)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'lead') {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:tersedia,dipinjam,maintenance',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('assets', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $asset = Asset::create($validated);
        return response()->json(['message' => 'Aset berhasil ditambahkan', 'data' => $asset]);
    }

    public function updateAsset(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'lead') {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:tersedia,dipinjam,maintenance',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if ($asset->photo && !filter_var($asset->photo, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $asset->photo));
            }
            $path = $request->file('photo')->store('assets', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $asset->update($validated);
        return response()->json(['message' => 'Aset berhasil diupdate', 'data' => $asset]);
    }

    public function destroyAsset($id)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'lead') {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $asset = Asset::findOrFail($id);
        
        // Cek jika aset masih ada peminjaman aktif
        $activeBorrowings = BorrowingRequest::where('asset_id', $id)
            ->whereIn('status', ['pending', 'approved', 'overdue'])
            ->exists();
            
        if ($activeBorrowings) {
            return response()->json(['error' => 'Aset tidak bisa dihapus karena masih dalam status dipinjam atau menunggu persetujuan.'], 400);
        }

        if ($asset->photo && !filter_var($asset->photo, FILTER_VALIDATE_URL)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $asset->photo));
        }

        $asset->delete();
        return response()->json(['message' => 'Aset berhasil dihapus']);
    }
}
