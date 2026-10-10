<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        return view('dashboard', [
            'hasSignature' => Signature::isUsableFor($userId),
            'pendingCount' => Document::where('user_id', $userId)->where('status', 'pending')->count(),
            'signedCount' => Document::where('user_id', $userId)->where('status', 'signed')->count(),
            'recentEvents' => HistoryController::eventsFor($userId)->take(5),
        ]);
    }
}
