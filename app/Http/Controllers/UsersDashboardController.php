<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ApiTokenAbility;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user.view')->only(['index']);
        $this->middleware('permission:user.create')->only(['create', 'store']);
        $this->middleware('permission:user.update')->only(['edit', 'update']);
        $this->middleware('permission:user.delete')->only(['destroy']);
        $this->middleware('permission:user.access.manage')->only(['access', 'updateAccess']);
        $this->middleware('permission:api.token.manage')->only(['storeApiToken', 'storeQuickApiToken', 'destroyApiToken']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->renderUserIndex($request);
    }

    public function apiAccounts(Request $request)
    {
        return $this->renderUserIndex($request, 'api', 'Daftar Akun API', true);
    }

    private function renderUserIndex(Request $request, ?string $forcedAccessType = null, string $title = 'Daftar User', bool $isApiAccountsPage = false)
    {
        $judul = $title;
        $q = trim((string) $request->input('q', ''));
        $instansiUuid = $request->query('instansi_uuid');
        $accessType = $forcedAccessType ?? (string) $request->input('access_type', '');

        // eager load pegawai + instansi
        $query = User::with(['pegawai.instansi', 'roles.permissions', 'permissions']);

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('email', 'like', "%{$q}%")
                   ->orWhereHas('pegawai', function ($pq) use ($q) {
                       $pq->where('nama', 'like', "%{$q}%")
                          ->orWhere('nip', 'like', "%{$q}%")
                          ->orWhereHas('instansi', function ($inq) use ($q) {
                              $inq->where('nama_instansi', 'like', "%{$q}%");
                          });
                   });
            });
        }

        // filter by instansi_uuid if provided (uses pegawai.instansi relation / instansi_uuid field)
        if (! empty($instansiUuid)) {
            $query->whereHas('pegawai', function ($pq) use ($instansiUuid) {
                $pq->where('instansi_uuid', $instansiUuid);
            });
        }

        if ($accessType === 'api') {
            $query->where(function ($permissionQuery) {
                $permissionQuery->whereHas('permissions', function ($directPermissionQuery) {
                    $directPermissionQuery->where('name', 'api.login');
                })->orWhereHas('roles.permissions', function ($rolePermissionQuery) {
                    $rolePermissionQuery->where('name', 'api.login');
                });
            });
        }

        if ($accessType === 'api-only') {
            $query->where(function ($permissionQuery) {
                $permissionQuery->whereHas('permissions', function ($directPermissionQuery) {
                    $directPermissionQuery->where('name', 'api.login');
                })->orWhereHas('roles.permissions', function ($rolePermissionQuery) {
                    $rolePermissionQuery->where('name', 'api.login');
                });
            })->where(function ($permissionQuery) {
                $permissionQuery->whereDoesntHave('permissions', function ($directPermissionQuery) {
                    $directPermissionQuery->where('name', 'web.login');
                })->whereDoesntHave('roles.permissions', function ($rolePermissionQuery) {
                    $rolePermissionQuery->where('name', 'web.login');
                });
            });
        }

        if ($accessType === 'web-only') {
            $query->where(function ($permissionQuery) {
                $permissionQuery->whereHas('permissions', function ($directPermissionQuery) {
                    $directPermissionQuery->where('name', 'web.login');
                })->orWhereHas('roles.permissions', function ($rolePermissionQuery) {
                    $rolePermissionQuery->where('name', 'web.login');
                });
            })->where(function ($permissionQuery) {
                $permissionQuery->whereDoesntHave('permissions', function ($directPermissionQuery) {
                    $directPermissionQuery->where('name', 'api.login');
                })->whereDoesntHave('roles.permissions', function ($rolePermissionQuery) {
                    $rolePermissionQuery->where('name', 'api.login');
                });
            });
        }

        $items = $query->orderBy('email')->paginate(15)->appends([
            'q' => $q,
            'instansi_uuid' => $instansiUuid,
            'access_type' => $accessType,
        ]);

        return view('admin.konfigurasi.users.index', compact('judul', 'items', 'q', 'instansiUuid', 'accessType', 'isApiAccountsPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $judul = 'Buat Data User';

        // optional filter by instansi_uuid from request (allows select to be pre-filtered)
        $instansiUuid = $request->query('instansi_uuid');

        $pegawaiQuery = Pegawai::with('instansi')
            ->where('del', 0)
            ->whereNull('deleted_at')
            ->where('user_status', 0);

        if (! empty($instansiUuid)) {
            $pegawaiQuery->where('instansi_uuid', $instansiUuid);
        }

        $items = $pegawaiQuery->get();

        return view('admin.konfigurasi.users.create', compact('judul','items','instansiUuid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'id_pegawai' => 'required|exists:pegawai,id',
            'password' => ['nullable','confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        DB::transaction(function () use ($validated) {
            if (empty($validated['password'])) {
                $plain = Str::random(24);
                $validated['password'] = Hash::make($plain);
                $user = User::create($validated);
                Password::sendResetLink(['email' => $validated['email']]);
            } else {
                $validated['password'] = Hash::make($validated['password']);
                $user = User::create($validated);
            }

            Pegawai::where('id', $validated['id_pegawai'])
                ->whereNull('deleted_at')
                ->update(['user_status' => 1]);
        });

        return redirect('/konfigurasi/user')->with('success', 'User Baru Berhasil di Tambahkan !');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $judul = 'Edit Data User';

        // daftar pegawai yang boleh dipilih:
        // - pegawai aktif & belum punya user
        // - plus pegawai yang saat ini terkait dengan user ini (agar tetap muncul)
        $items = Pegawai::with('instansi')
            ->where('del', 0)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($user) {
                $q->where('user_status', 0);
                if ($user->id_pegawai) {
                    $q->orWhere('id', $user->id_pegawai);
                }
            })
            ->get();

        return view('admin.konfigurasi.users.edit', compact('judul','user','items'));
    }

    /**
     * Show the form for managing user access.
     */
    public function access(Request $request, User $user)
    {
        $judul = 'Kelola Akses User';
        $user->load(['pegawai.instansi', 'roles', 'permissions']);
        $tokenStatus = (string) $request->query('token_status', '');
        $availableTokenAbilities = ApiTokenAbility::adminSelectableAbilities();

        $roles = Role::query()->orderBy('name')->get();
        $permissions = Permission::query()->orderBy('name')->get();
        $importPermissions = $permissions
            ->filter(fn (Permission $permission) => Str::endsWith($permission->name, '.import'))
            ->values();
        $permissionGroups = $permissions
            ->reject(fn (Permission $permission) => Str::endsWith($permission->name, '.import'))
            ->groupBy(function (Permission $permission) {
            $prefix = preg_split('/[._-]/', $permission->name)[0] ?? 'lainnya';

            return Str::headline($prefix);
            });

        $effectivePermissions = $user->getAllPermissions()->sortBy('name')->values();
        $apiTokensQuery = PersonalAccessToken::query()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->orderByDesc('id');

        if ($tokenStatus === 'active') {
            $apiTokensQuery->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
        }

        if ($tokenStatus === 'expired') {
            $apiTokensQuery->whereNotNull('expires_at')
                ->where('expires_at', '<', now());
        }

        $apiTokens = $apiTokensQuery->get();

        return view('admin.konfigurasi.users.access', compact(
            'judul',
            'user',
            'roles',
            'importPermissions',
            'permissionGroups',
            'effectivePermissions',
            'apiTokens',
            'tokenStatus',
            'availableTokenAbilities'
        ));
    }

    /**
     * Update the specified user's access.
     */
    public function updateAccess(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->syncRoles($validated['roles'] ?? []);
            $user->syncPermissions($validated['permissions'] ?? []);
        });

        return redirect()
            ->route('konfigurasi.user.access', $user)
            ->with('success', 'Hak akses user berhasil diperbarui.');
    }

    public function storeApiToken(Request $request, User $user)
    {
        if (! $user->can('api.login')) {
            return redirect()
                ->route('konfigurasi.user.access', $user)
                ->with('error', 'User ini belum memiliki izin login API. Tambahkan permission api.login atau role api-client terlebih dahulu.');
        }

        $validated = $request->validate([
            'token_name' => ['required', 'string', 'max:100'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['string', 'in:'.implode(',', array_keys(ApiTokenAbility::adminSelectableAbilities()))],
        ]);

        $token = $user->createToken(
            trim($validated['token_name']),
            array_values(array_unique($validated['abilities'])),
            ! empty($validated['expires_at']) ? now()->parse($validated['expires_at']) : null
        );

        return redirect()
            ->route('konfigurasi.user.access', $user)
            ->with('success', 'Token API berhasil dibuat.')
            ->with('generatedApiToken', [
                'id' => $token->accessToken->id,
                'name' => trim($validated['token_name']),
                'abilities' => array_values(array_unique($validated['abilities'])),
                'plain_text' => $token->plainTextToken,
                'expires_at' => optional($token->accessToken->expires_at)->toDateTimeString(),
            ]);
    }

    public function storeQuickApiToken(Request $request, User $user)
    {
        if (! $user->can('api.login')) {
            return redirect()
                ->back()
                ->with('error', 'User ini belum memiliki izin login API. Tambahkan permission api.login atau role api-client terlebih dahulu.');
        }

        $tokenName = 'auto-'.now()->format('Ymd-His').'-'.substr(md5($user->email), 0, 6);
        $token = $user->createToken($tokenName, ApiTokenAbility::defaultAbilities(), $this->resolveDefaultTokenExpiration());

        return redirect()
            ->back()
            ->with('success', 'Token API otomatis berhasil dibuat.')
            ->with('generatedApiToken', [
                'id' => $token->accessToken->id,
                'name' => $tokenName,
                'abilities' => ApiTokenAbility::defaultAbilities(),
                'plain_text' => $token->plainTextToken,
                'expires_at' => optional($token->accessToken->expires_at)->toDateTimeString(),
            ]);
    }

    public function destroyApiToken(User $user, int $tokenId)
    {
        $token = PersonalAccessToken::query()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->findOrFail($tokenId);

        $token->delete();

        return redirect()
            ->route('konfigurasi.user.access', $user)
            ->with('success', 'Token API berhasil dicabut.');
    }

    private function resolveDefaultTokenExpiration()
    {
        $configuredMinutes = config('sanctum.expiration');

        if (is_numeric($configuredMinutes) && (int) $configuredMinutes > 0) {
            return now()->addMinutes((int) $configuredMinutes);
        }

        return now()->addMinutes(120);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable','confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
            'id_pegawai' => 'required|exists:pegawai,id'
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        DB::transaction(function () use ($user, $validated) {
            $oldPegawaiId = $user->id_pegawai;
            $user->update($validated);

            Pegawai::where('id', $validated['id_pegawai'])->whereNull('deleted_at')->update(['user_status' => 1]);

            if (! empty($oldPegawaiId) && $oldPegawaiId != $validated['id_pegawai']) {
                Pegawai::where('id', $oldPegawaiId)->whereNull('deleted_at')->update(['user_status' => 0]);
            }
        });

        return redirect('/konfigurasi/user')->with('success', 'Update User Berhasil !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $pegawaiId = $user->id_pegawai;
        User::destroy($user->id);

        if ($pegawaiId) {
            Pegawai::where('id', $pegawaiId)->whereNull('deleted_at')->update(['user_status' => 0]);
        }

        return redirect('/konfigurasi/user')->with('success', 'Hapus User Berhasil !');
    }
}
