<?php

namespace App\Models;

use Database\Factories\PlatformSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    /** @use HasFactory<PlatformSettingFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public const DEFAULTS = [
        'minimum_withdrawal' => 100000,
        'watermark_path' => 'images/watermarksistemjepret.png',
        'watermark_opacity' => 55,
        'watermark_scale' => 30,
        'watermark_position' => 'center',
        'strip_gps' => true,
        'retain_camera_metadata' => true,
    ];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public static function values(): array
    {
        return array_replace(self::DEFAULTS, static::query()->get()->pluck('value', 'key')->all());
    }

    public static function minimumWithdrawal(): int
    {
        return (int) (static::where('key', 'minimum_withdrawal')->first()?->value ?? self::DEFAULTS['minimum_withdrawal']);
    }
}
