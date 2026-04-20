<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiAuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Audit Log API';

        $query = $this->buildFilteredQuery($request)->orderByDesc('id');

        $entries = $query->paginate(100)->withQueryString();
        $groups = ApiAuditLog::query()->select('api_group')->distinct()->orderBy('api_group')->pluck('api_group');
        $summary = $this->buildSummary($request);

        return view('admin.api.audits.index', compact('judul', 'entries', 'groups', 'summary'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'api-audit-logs-'.now()->format('Ymd-His').'.csv';
        $rows = $this->buildFilteredQuery($request)->orderByDesc('id')->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'id', 'created_at', 'client_name', 'token_name', 'api_group', 'route_name', 'method',
                'path', 'status_code', 'duration_ms', 'user_id', 'ip_address', 'authenticated', 'error_message'
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    optional($row->created_at)->toDateTimeString(),
                    $row->client_name,
                    $row->token_name,
                    $row->api_group,
                    $row->route_name,
                    $row->method,
                    $row->path,
                    $row->status_code,
                    $row->duration_ms,
                    $row->user_id,
                    $row->ip_address,
                    $row->authenticated ? 'yes' : 'no',
                    $row->error_message,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(ApiAuditLog $apiAuditLog)
    {
        $judul = 'Detail Audit Log API';

        return view('admin.api.audits.show', compact('judul', 'apiAuditLog'));
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = ApiAuditLog::query();

        if ($request->filled('method')) {
            $query->where('method', strtoupper((string) $request->input('method')));
        }

        if ($request->filled('status')) {
            $query->where('status_code', (int) $request->input('status'));
        }

        if ($request->filled('group')) {
            $query->where('api_group', (string) $request->input('group'));
        }

        if ($request->filled('client')) {
            $query->where('client_name', 'like', '%'.trim((string) $request->input('client')).'%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', (string) $request->input('date_to'));
        }

        return $query;
    }

    private function buildSummary(Request $request): array
    {
        $baseQuery = $this->buildFilteredQuery($request);

        $totalRequests = (clone $baseQuery)->count();
        $successRequests = (clone $baseQuery)->where('status_code', '<', 400)->count();
        $errorRequests = (clone $baseQuery)->where('status_code', '>=', 400)->count();
        $averageDuration = round((float) ((clone $baseQuery)->avg('duration_ms') ?? 0), 2);

        $topClients = (clone $baseQuery)
            ->selectRaw("COALESCE(NULLIF(client_name, ''), 'Tidak Diketahui') as client_name, COUNT(*) as total")
            ->groupByRaw("COALESCE(NULLIF(client_name, ''), 'Tidak Diketahui')")
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $statusBreakdown = (clone $baseQuery)
            ->selectRaw('status_code, COUNT(*) as total')
            ->groupBy('status_code')
            ->orderBy('status_code')
            ->get();

        $since = $request->filled('date_from')
            ? Carbon::parse((string) $request->input('date_from'))
            : now()->subDays(6)->startOfDay();
        $until = $request->filled('date_to')
            ? Carbon::parse((string) $request->input('date_to'))
            : now()->endOfDay();

        $dailyRows = (clone $baseQuery)
            ->whereBetween('created_at', [$since, $until])
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        $dailyCounts = [];
        foreach ($dailyRows as $row) {
            $dailyCounts[$row->tanggal] = (int) $row->total;
        }

        return [
            'total_requests' => $totalRequests,
            'success_requests' => $successRequests,
            'error_requests' => $errorRequests,
            'average_duration' => $averageDuration,
            'top_clients' => $topClients,
            'status_breakdown' => $statusBreakdown,
            'daily_counts' => $dailyCounts,
        ];
    }
}