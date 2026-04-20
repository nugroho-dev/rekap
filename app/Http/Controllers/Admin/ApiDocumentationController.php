<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        $judul = 'REST API';
        $baseUrl = url('/api');
        $sampleToken = 'your-sanctum-token';
        $authHeaderSample = "Authorization: Bearer {$sampleToken}\nAccept: application/json";
        $refreshBeforeSeconds = max((((int) config('sanctum.expiration', 120)) * 60) - 300, 0);
        $authEndpoints = [
            [
                'method' => 'POST',
                'path' => '/auth/login',
                'name' => 'Login token',
                'description' => 'Membuat token Sanctum untuk aplikasi eksternal.',
                'sample' => [
                    'email' => 'admin@example.com',
                    'password' => 'password',
                    'device_name' => 'external-dashboard',
                ],
                'curl' => implode(" \\\n+", [
                    'curl --request POST \''.$baseUrl.'/auth/login\'',
                    '--header \''.'Accept: application/json'.'\'',
                    '--header \''.'Content-Type: application/json'.'\'',
                    '--data \''.'{"email":"admin@example.com","password":"password","device_name":"external-dashboard"}'.'\'',
                ]),
                'response' => [
                    'access_token' => '1|sample-sanctum-token',
                    'token_type' => 'Bearer',
                    'abilities' => [
                        'auth.session',
                        'statistik.non-berusaha.read',
                        'statistik.berusaha.read',
                    ],
                    'expires_at' => now()->addMinutes((int) config('sanctum.expiration', 120))->toIso8601String(),
                    'refresh_before_seconds' => $refreshBeforeSeconds,
                    'user' => [
                        'id' => 7,
                        'email' => 'admin@example.com',
                    ],
                ],
                'fetch' => implode("\n", [
                    'fetch(\''.$baseUrl.'/auth/login\', {',
                    '  method: "POST",',
                    '  headers: {',
                    '    "Accept": "application/json",',
                    '    "Content-Type": "application/json"',
                    '  },',
                    '  body: JSON.stringify({',
                    '    email: "admin@example.com",',
                    '    password: "password",',
                    '    device_name: "external-dashboard"',
                    '  })',
                    '}).then(response => response.json());',
                ]),
            ],
            [
                'method' => 'GET',
                'path' => '/auth/me',
                'name' => 'Profil token aktif',
                'description' => 'Mengambil data user dan token yang sedang dipakai.',
                'auth' => true,
                'curl' => implode(" \\\n+", [
                    'curl --request GET \''.$baseUrl.'/auth/me\'',
                    '--header \''.'Accept: application/json'.'\'',
                    '--header \''.'Authorization: Bearer '.$sampleToken.'\'',
                ]),
                'response' => [
                    'user' => [
                        'id' => 7,
                        'email' => 'admin@example.com',
                    ],
                    'current_token' => [
                        'name' => 'external-dashboard',
                    ],
                ],
                'fetch' => implode("\n", [
                    'fetch(\''.$baseUrl.'/auth/me\', {',
                    '  method: "GET",',
                    '  headers: {',
                    '    "Accept": "application/json",',
                    '    "Authorization": "Bearer '.$sampleToken.'"',
                    '  }',
                    '}).then(response => response.json());',
                ]),
            ],
            [
                'method' => 'POST',
                'path' => '/auth/refresh',
                'name' => 'Refresh token aktif',
                'description' => 'Menerbitkan token baru dari token aktif, lalu mencabut token lama.',
                'auth' => true,
                'curl' => implode(" \\\n+", [
                    'curl --request POST \''.$baseUrl.'/auth/refresh\'',
                    '--header \''.'Accept: application/json'.'\'',
                    '--header \''.'Authorization: Bearer '.$sampleToken.'\'',
                ]),
                'response' => [
                    'message' => 'Refresh token berhasil.',
                    'access_token' => '2|refreshed-sanctum-token',
                    'token_type' => 'Bearer',
                    'abilities' => [
                        'auth.session',
                        'statistik.non-berusaha.read',
                        'statistik.berusaha.read',
                    ],
                    'expires_at' => now()->addMinutes((int) config('sanctum.expiration', 120))->toIso8601String(),
                    'refresh_before_seconds' => $refreshBeforeSeconds,
                ],
                'fetch' => implode("\n", [
                    'fetch(\''.$baseUrl.'/auth/refresh\', {',
                    '  method: "POST",',
                    '  headers: {',
                    '    "Accept": "application/json",',
                    '    "Authorization": "Bearer '.$sampleToken.'"',
                    '  }',
                    '}).then(response => response.json());',
                ]),
            ],
            [
                'method' => 'POST',
                'path' => '/auth/logout',
                'name' => 'Logout token aktif',
                'description' => 'Mencabut token yang dipakai pada request saat ini.',
                'auth' => true,
                'curl' => implode(" \\\n+", [
                    'curl --request POST \''.$baseUrl.'/auth/logout\'',
                    '--header \''.'Accept: application/json'.'\'',
                    '--header \''.'Authorization: Bearer '.$sampleToken.'\'',
                ]),
                'response' => [
                    'message' => 'Token berhasil dicabut.',
                ],
                'fetch' => implode("\n", [
                    'fetch(\''.$baseUrl.'/auth/logout\', {',
                    '  method: "POST",',
                    '  headers: {',
                    '    "Accept": "application/json",',
                    '    "Authorization": "Bearer '.$sampleToken.'"',
                    '  }',
                    '}).then(response => response.json());',
                ]),
            ],
        ];

        $refreshGuidance = [
            'Client menyimpan access_token, expires_at, dan refresh_before_seconds setelah login.',
            'Client menjadwalkan refresh setelah refresh_before_seconds terlewati, sebelum expires_at tercapai.',
            'Client memanggil POST /auth/refresh menggunakan bearer token aktif.',
            'Client mengganti token lama dengan access_token baru dari response refresh.',
            'Jika beberapa request mendapat 401 bersamaan, semua request harus menunggu satu proses refresh yang sama, bukan memanggil refresh berulang-ulang.',
            'Jika refresh gagal dengan 401, client harus mengarahkan proses ke login ulang.',
        ];

        $retryExample = implode("\n", [
            'let accessToken = "your-sanctum-token";',
            'let refreshPromise = null;',
            '',
            'async function refreshAccessToken() {',
            '  const response = await fetch("'.$baseUrl.'/auth/refresh", {',
            '    method: "POST",',
            '    headers: {',
            '      "Accept": "application/json",',
            '      "Authorization": `Bearer ${accessToken}`',
            '    }',
            '  });',
            '',
            '  if (!response.ok) {',
            '    throw new Error("Refresh token gagal");',
            '  }',
            '',
            '  const payload = await response.json();',
            '  accessToken = payload.access_token;',
            '',
            '  return accessToken;',
            '}',
            '',
            'async function apiRequest(url, options = {}, hasRetried = false) {',
            '  const response = await fetch(url, {',
            '    ...options,',
            '    headers: {',
            '      "Accept": "application/json",',
            '      ...(options.headers || {}),',
            '      "Authorization": `Bearer ${accessToken}`',
            '    }',
            '  });',
            '',
            '  if (response.status !== 401 || hasRetried) {',
            '    return response;',
            '  }',
            '',
            '  // Bagikan satu refresh promise agar request paralel tidak memicu refresh berkali-kali.',
            '  refreshPromise = refreshPromise || refreshAccessToken().finally(() => {',
            '    refreshPromise = null;',
            '  });',
            '',
            '  await refreshPromise;',
            '',
            '  return apiRequest(url, options, true);',
            '}',
            '',
            'const response = await apiRequest("'.$baseUrl.'/statistik/berusaha/proyek?year='.now()->year.'&semester=1");',
            'const data = await response.json();',
        ]);

        $axiosRetryExample = implode("\n", [
            'import axios from "axios";',
            '',
            'const api = axios.create({',
            '  baseURL: "'.$baseUrl.'",',
            '  headers: {',
            '    "Accept": "application/json"',
            '  }',
            '});',
            '',
            'let accessToken = "your-sanctum-token";',
            'let refreshPromise = null;',
            '',
            'api.interceptors.request.use((config) => {',
            '  config.headers = config.headers || {};',
            '  config.headers.Authorization = `Bearer ${accessToken}`;',
            '',
            '  return config;',
            '});',
            '',
            'async function refreshAccessToken() {',
            '  const response = await api.post("/auth/refresh");',
            '  accessToken = response.data.access_token;',
            '',
            '  return accessToken;',
            '}',
            '',
            'api.interceptors.response.use(',
            '  (response) => response,',
            '  async (error) => {',
            '    const originalRequest = error.config;',
            '',
            '    if (!originalRequest || error.response?.status !== 401 || originalRequest._hasRetried) {',
            '      return Promise.reject(error);',
            '    }',
            '',
            '    originalRequest._hasRetried = true;',
            '    refreshPromise = refreshPromise || refreshAccessToken().finally(() => {',
            '      refreshPromise = null;',
            '    });',
            '',
            '    await refreshPromise;',
            '    originalRequest.headers.Authorization = `Bearer ${accessToken}`;',
            '',
            '    return api(originalRequest);',
            '  }',
            ');',
            '',
            'const response = await api.get("/statistik/berusaha/proyek", {',
            '  params: { year: '.now()->year.', semester: 1 }',
            '});',
            '',
            'console.log(response.data);',
        ]);

        $apiErrorFormat = [
            'message' => 'Unauthenticated.',
            'code' => 'UNAUTHENTICATED',
            'status' => 401,
            'errors' => null,
        ];

        $validationErrorFormat = [
            'message' => 'The email field is required. (and 2 more errors)',
            'code' => 'VALIDATION_ERROR',
            'status' => 422,
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
                'device_name' => ['The device name field is required.'],
            ],
        ];

        $modules = [
            [
                'group' => 'Statistik Non Berusaha',
                'prefix' => '/statistik/non-berusaha',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/simpel', 'name' => 'Simpel'],
                    ['method' => 'GET', 'path' => '/simbg', 'name' => 'SimBG'],
                    ['method' => 'GET', 'path' => '/pbg', 'name' => 'PBG'],
                    ['method' => 'GET', 'path' => '/sicantik', 'name' => 'SiCantik'],
                    ['method' => 'GET', 'path' => '/mppd', 'name' => 'MPPD'],
                ],
            ],
            [
                'group' => 'Statistik Berusaha',
                'prefix' => '/statistik/berusaha',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/proyek', 'name' => 'Proyek'],
                    ['method' => 'GET', 'path' => '/nib', 'name' => 'NIB'],
                    ['method' => 'GET', 'path' => '/izin', 'name' => 'Izin'],
                ],
            ],
        ];

        $commonParams = [
            ['name' => 'year', 'description' => 'Tahun data, contoh 2026.'],
            ['name' => 'semester', 'description' => 'Nilai 1 atau 2 untuk membatasi semester.'],
        ];

        foreach ($modules as &$module) {
            foreach ($module['endpoints'] as &$endpoint) {
                $fullPath = $module['prefix'].$endpoint['path'];
                $endpoint['curl'] = implode(" \\\n+", [
                    'curl --request GET \''.$baseUrl.$fullPath.'?year='.now()->year.'&semester=1\'',
                    '--header \''.'Accept: application/json'.'\'',
                    '--header \''.'Authorization: Bearer '.$sampleToken.'\'',
                ]);
                $endpoint['response'] = [
                    'module' => strtolower($endpoint['name']),
                    'title' => 'Contoh '.$endpoint['name'],
                    'generated_at' => now()->toIso8601String(),
                    'data' => [
                        'filters' => [
                            'year' => now()->year,
                            'semester' => '1',
                        ],
                        'summary' => [
                            'total' => 120,
                            'total_terbit' => 45,
                        ],
                        'charts' => [
                            'monthly_counts' => [1 => 10, 2 => 15, 3 => 20],
                        ],
                        'stats' => [
                            ['kategori' => 'Contoh', 'jumlah' => 45],
                        ],
                    ],
                ];
                $endpoint['fetch'] = implode("\n", [
                    'fetch(\''.$baseUrl.$fullPath.'?year='.now()->year.'&semester=1\', {',
                    '  method: "GET",',
                    '  headers: {',
                    '    "Accept": "application/json",',
                    '    "Authorization": "Bearer '.$sampleToken.'"',
                    '  }',
                    '}).then(response => response.json());',
                ]);
            }
        }
        unset($module, $endpoint);

        return view('admin.api.index', compact('judul', 'baseUrl', 'authEndpoints', 'modules', 'commonParams', 'sampleToken', 'authHeaderSample', 'refreshGuidance', 'retryExample', 'axiosRetryExample', 'apiErrorFormat', 'validationErrorFormat'));
    }
}