<?php

namespace App\Http\Controllers;

use App\Models\TrustedIp;
use Illuminate\Http\Request;

class TrustedIpController extends Controller
{
    public function index()
    {
        $trustedIps = TrustedIp::with('addedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('dashboard.security.trusted-ips.index', compact('trustedIps'));
    }

    public function destroy(TrustedIp $trustedIp)
    {
        $trustedIp->delete();
        return redirect()->route('security.trusted-ips.index')
            ->with('success', 'تم إزالة IP من القائمة الموثوقة');
    }
}
