<?php

namespace CTKM\Enums;

abstract class BaseEnum
{
    public const MAP = [];

    /**
     * Trả về toàn bộ MAP [key => label]
     */
    public static function options(): array
    {
        return static::MAP;
    }

    /**
     * Trả về key (array_keys)
     */
    public static function keys(): array
    {
        return array_keys(static::MAP);
    }

    /**
     * Trả về label theo key
     */
    public static function label(string $key): string
    {
        return static::MAP[$key] ?? $key;
    }

    /**
     * Trả về key theo label (nếu cần)
     */
    public static function keyFromLabel(string $label): ?string
    {
        $found = array_search($label, static::MAP, true);
        return $found !== false ? $found : null;
    }

    /**
     * Kiểm tra xem key có hợp lệ không
     */
    public static function isValid(string $key): bool
    {
        return array_key_exists($key, static::MAP);
    }
}
