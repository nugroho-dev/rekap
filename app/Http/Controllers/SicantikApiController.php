<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SicantikApiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'cari' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $cari = (string) $request->input('cari', '');
        $page = max(1, (int) $request->input('page', 1));
        $per_page = min(100, max(1, (int) $request->input('per_page', 25)));

        // keep some compatibility variables used by the view
        $dispage = $page * $per_page;
        $disfipage = ($dispage - $per_page) + 1;
        $disfipagedata = ($page - 1) * $per_page;
        if ($disfipagedata === 0) {
            $disfipagedata = null; // prefer null instead of string 'null'
        }

        $previous = max(1, $page - 1);
        $next = $page + 1;

        $base = config('services.sicantik.url', 'https://sicantik.go.id');
        $path = '/api/TemplateData/keluaran/38416.json';

        try {
            $response = Http::retry(3, 500)->timeout(10)->get($base . $path, [
                'page' => $disfipagedata,
                'per_page' => $per_page,
                'cari' => $cari,
            ]);
        } catch (\Exception $e) {
            Log::error('Sicantik API request failed', ['exception' => $e->getMessage()]);
            $items = [];
            $count = 0;
            $totalpage = 0;
            $secondlast = 0;
            return view('home', compact('items', 'count', 'page', 'per_page', 'dispage', 'disfipage', 'totalpage', 'previous', 'next', 'secondlast'));
        }

        if (!$response->ok()) {
            Log::error('Sicantik API returned non-OK status', ['status' => $response->status(), 'body' => $response->body()]);
            $items = [];
            $count = 0;
            $totalpage = 0;
            $secondlast = 0;
        } else {
            $data = $response->json();
            $items = data_get($data, 'data.data', []);
            $count = (int) data_get($data, 'data.count.0.data', 0);
            $totalpage = $per_page > 0 ? (int) ceil($count / $per_page) : 0;
            $secondlast = max(1, $totalpage - 1);
        }

        return view('home', compact('items', 'count', 'page', 'per_page', 'dispage', 'disfipage', 'disfipagedata', 'totalpage', 'previous', 'next', 'secondlast'));
    }
    public function kirim()
    {
        $id = request('id');
        $base = config('services.sicantik.url', 'https://sicantik.go.id');
        $path = '/api/TemplateData/keluaran/42533.json';
        try {
            $response = Http::retry(3, 500)->timeout(10)->get($base . $path, ['key_id' => $id]);
        } catch (\Exception $e) {
            Log::error('Sicantik kirim request failed', ['exception' => $e->getMessage(), 'id' => $id]);
            abort(500, 'External API request failed');
        }

        if (!$response->ok()) {
            Log::error('Sicantik kirim returned non-OK', ['status' => $response->status(), 'body' => $response->body()]);
            abort(500, 'External API returned error');
        }

        $data = $response->json();
        $items = data_get($data, 'data.data.0', []);

        return view('kirim', compact('items', 'id'));
    }
    public function dokumen(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'pesan' => 'nullable|string',
            'tujuan' => 'nullable|string',
            'link' => 'nullable|url',
        ]);

        $id = $request->input('id');
        $pesan = (string) $request->input('pesan', '');
        $tujuan = (string) $request->input('tujuan', '');
        $link = (string) $request->input('link', '');

        $base = config('services.sicantik.url', 'https://sicantik.go.id');
        $path = '/api/TemplateData/keluaran/42533.json';

        $statuspesan = 'kosong';
        if (empty($pesan) && empty($tujuan) && empty($link)) {
            try {
                $response = Http::retry(3, 500)->timeout(10)->get($base . $path, ['key_id' => $id]);
            } catch (\Exception $e) {
                Log::error('Sicantik dokumen request failed', ['exception' => $e->getMessage(), 'id' => $id]);
                abort(500, 'External API request failed');
            }
        } else {
            // send the message first (internal API)
            try {
                $responsepesan = Http::retry(3, 500)->timeout(10)->get('http://172.18.185.247:3000/api', [
                    'tujuan' => $tujuan,
                    'pesan' => $pesan,
                    'link' => $link,
                ]);
            } catch (\Exception $e) {
                Log::error('Internal send API failed', ['exception' => $e->getMessage()]);
                $responsepesan = null;
            }

            try {
                $response = Http::retry(3, 500)->timeout(10)->get($base . $path, ['key_id' => $id]);
            } catch (\Exception $e) {
                Log::error('Sicantik dokumen request failed', ['exception' => $e->getMessage(), 'id' => $id]);
                abort(500, 'External API request failed');
            }

            if ($responsepesan && $responsepesan->ok()) {
                $datapesan = $responsepesan->json();
                $statuspesan = data_get($datapesan, 'status', $statuspesan);
            }
        }

        if (!isset($response) || !$response->ok()) {
            Log::error('Sicantik dokumen returned non-OK', ['response' => isset($response) ? $response->body() : null]);
            abort(500, 'External API returned error');
        }

        $data = $response->json();
        $items = data_get($data, 'data.data', []);
        $nohp = data_get($data, 'data.data.0.no_hp');
        $jenis_izin = data_get($data, 'data.data.0.jenis_izin');
        $nama = data_get($data, 'data.data.0.nama');
        $no_permohonan = data_get($data, 'data.data.0.no_izin');

        $userphonegsm = $this->normalizePhoneForGsm((string) $nohp);
        return view('dokumen', compact('items', 'id', 'userphonegsm', 'jenis_izin', 'nama', 'no_permohonan', 'statuspesan'));
    }

    /**
     * Normalize phone numbers for display (returns leading 0, e.g. 0812...)
     */
    private function normalizePhoneForDisplay(string $number): string
    {
        $n = trim(strip_tags($number));
        $n = str_replace([' ', '(', ')', '.'], '', $n);
        if (!preg_match('/[^+0-9]/', $n)) {
            if (str_starts_with($n, '+62')) {
                return '0' . substr($n, 3);
            }
            if (str_starts_with($n, '62')) {
                return '0' . substr($n, 2);
            }
            if (str_starts_with($n, '8')) {
                return '0' . $n;
            }
            if (str_starts_with($n, '0')) {
                return '0' . substr($n, 1);
            }
        }
        return $n;
    }

    /**
     * Normalize phone numbers for GSM/internal API (no leading zero, e.g. 812...)
     */
    private function normalizePhoneForGsm(string $number): string
    {
        $n = trim(strip_tags($number));
        $n = str_replace([' ', '(', ')', '.'], '', $n);
        if (!preg_match('/[^+0-9]/', $n)) {
            if (str_starts_with($n, '+62')) {
                return substr($n, 3);
            }
            if (str_starts_with($n, '62')) {
                return substr($n, 2);
            }
            if (str_starts_with($n, '0')) {
                return substr($n, 1);
            }
            return $n;
        }
        return $n;
    }
}
