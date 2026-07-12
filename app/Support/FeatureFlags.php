<?php

namespace App\Support;

use App\Domain\Shop\Models\Setting;
use Illuminate\Support\Facades\App;

class FeatureFlags
{
    public static function inventoryEnabled(): bool
    {
        return static::resolve('inventory_enabled', false);
    }

    public static function branchesEnabled(): bool
    {
        return static::resolve('branches_enabled', false);
    }

    public static function modifiersEnabled(): bool
    {
        return static::resolve('modifier_groups_enabled', false);
    }

    public static function variantsEnabled(): bool
    {
        return static::resolve('variants_enabled', false);
    }

    public static function tablesEnabled(): bool
    {
        return static::resolve('tables_enabled', false);
    }

    public static function digitalMenuEnabled(): bool
    {
        return static::resolve('digital_menu_enabled', false);
    }

    public static function notificationsEnabled(): bool
    {
        return static::resolve('notifications_enabled', false);
    }

    private static function resolve(string $key, mixed $default): mixed
    {
        if (App::runningUnitTests()) {
            return $default;
        }

        try {
            return Setting::getValue($key, $default);
        } catch (\Exception) {
            return $default;
        }
    }
}
