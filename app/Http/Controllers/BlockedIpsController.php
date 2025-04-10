<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use Illuminate\Http\Request;

class BlockedIpsController extends Controller
{
    public function index()
    {
        $blockedIps = BlockedIp::with('blockedBy')
            ->latest('blocked_at')
            ->paginate(15);

        return view('dashboard.security.blocked-ips.index', compact('blockedIps'));
    }

    public function destroy(BlockedIp $blockedIp)
    {
        $blockedIp->delete();
        return redirect()->route('security.blocked-ips.index')
            ->with('success', 'تم إزالة IP من قائمة الحظر بنجاح');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blocked_ips,id'
        ]);

        BlockedIp::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة عناوين IP المحددة من قائمة الحظر بنجاح'
        ]);
    }
}
