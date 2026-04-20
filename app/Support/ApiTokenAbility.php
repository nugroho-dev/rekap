<?php

namespace App\Support;

class ApiTokenAbility
{
    public const AUTH_SESSION = 'auth.session';

    public const STATISTIK_NON_BERUSAHA_READ = 'statistik.non-berusaha.read';

    public const STATISTIK_BERUSAHA_READ = 'statistik.berusaha.read';

    public static function statistikAbilities(): array
    {
        return [
            self::STATISTIK_NON_BERUSAHA_READ,
            self::STATISTIK_BERUSAHA_READ,
        ];
    }

    public static function adminSelectableAbilities(): array
    {
        return [
            self::AUTH_SESSION => 'Sesi API dasar (me, logout)',
            self::STATISTIK_NON_BERUSAHA_READ => 'Statistik non-berusaha',
            self::STATISTIK_BERUSAHA_READ => 'Statistik berusaha',
        ];
    }

    public static function defaultAbilities(): array
    {
        return array_keys(self::adminSelectableAbilities());
    }
}