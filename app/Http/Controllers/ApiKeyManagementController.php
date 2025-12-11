<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Models\ApiLog;
use Illuminate\Support\Facades\DB;

class ApiKeyManagementController extends Controller
{
    /**
     * Display API key management dashboard.
     */
    public function index()
    {
        $apiKeys = ApiKey::where('user_id', auth()->id())
            ->withCount('logs')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics for the current user's API keys
        $apiKeyIds = $apiKeys->pluck('id');

        $totalRequests = ApiLog::whereIn('api_key_id', $apiKeyIds)->count();
        $todayRequests = ApiLog::whereIn('api_key_id', $apiKeyIds)
            ->whereDate('created_at', today())
            ->count();

        $avgResponseTime = ApiLog::whereIn('api_key_id', $apiKeyIds)
            ->avg('response_time');

        // Get recent logs
        $recentLogs = ApiLog::whereIn('api_key_id', $apiKeyIds)
            ->with('apiKey')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Get statistics for chart - last 7 days
        $last7Days = ApiLog::whereIn('api_key_id', $apiKeyIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get endpoint statistics
        $endpointStats = ApiLog::whereIn('api_key_id', $apiKeyIds)
            ->select(
                'endpoint',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(response_time) as avg_response_time')
            )
            ->groupBy('endpoint')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get status code distribution
        $statusCodeStats = ApiLog::whereIn('api_key_id', $apiKeyIds)
            ->select(
                'status_code',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status_code')
            ->orderBy('count', 'desc')
            ->get();

        return view('api.management', compact(
            'apiKeys',
            'totalRequests',
            'todayRequests',
            'avgResponseTime',
            'recentLogs',
            'last7Days',
            'endpointStats',
            'statusCodeStats'
        ));
    }

    /**
     * Store a new API key.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $apiKey = ApiKey::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'key' => ApiKey::generateKey(),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('api.management')
            ->with('success', 'API Key created successfully')
            ->with('new_key', $apiKey->key);
    }

    /**
     * Toggle API key status.
     */
    public function toggleStatus($id)
    {
        $apiKey = ApiKey::where('user_id', auth()->id())
            ->findOrFail($id);

        $apiKey->is_active = !$apiKey->is_active;
        $apiKey->save();

        return redirect()->route('api.management')
            ->with('success', 'API Key status updated successfully');
    }

    /**
     * Delete API key.
     */
    public function destroy($id)
    {
        $apiKey = ApiKey::where('user_id', auth()->id())
            ->findOrFail($id);

        $apiKey->delete();

        return redirect()->route('api.management')
            ->with('success', 'API Key deleted successfully');
    }

    /**
     * Show API documentation.
     */
    public function documentation()
    {
        return view('api.documentation');
    }
}

