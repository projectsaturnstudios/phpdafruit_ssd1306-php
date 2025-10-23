<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Shapes;

/**
 * Icon bitmap storage and configuration
 * 
 * Manages predefined icon bitmaps for common UI elements.
 */
class Icon
{
    private static array $icons = [];

    public function __construct(
        public string $name,
        public int $width,
        public int $height,
        public array $bitmap
    ) {}

    /**
     * Get icon by name
     */
    public static function get(string $name): ?self
    {
        return self::$icons[$name] ?? null;
    }

    /**
     * Register a new icon
     */
    public static function register(string $name, int $width, int $height, array $bitmap): void
    {
        self::$icons[$name] = new self($name, $width, $height, $bitmap);
    }

    /**
     * Check if icon exists
     */
    public static function has(string $name): bool
    {
        return isset(self::$icons[$name]);
    }

    /**
     * Get all registered icon names
     */
    public static function getNames(): array
    {
        return array_keys(self::$icons);
    }

    /**
     * Initialize built-in icons
     */
    public static function initializeBuiltIns(): void
    {
        // 8x8 checkmark icon
        self::register('checkmark', 8, 8, [
            0b00000000,
            0b00000001,
            0b00000011,
            0b10000110,
            0b11001100,
            0b01111000,
            0b00110000,
            0b00000000,
        ]);

        // 8x8 cross/X icon
        self::register('cross', 8, 8, [
            0b10000001,
            0b01000010,
            0b00100100,
            0b00011000,
            0b00011000,
            0b00100100,
            0b01000010,
            0b10000001,
        ]);

        // 8x8 warning icon
        self::register('warning', 8, 8, [
            0b00011000,
            0b00111100,
            0b01011010,
            0b01011010,
            0b01111110,
            0b01011010,
            0b01111110,
            0b01111110,
        ]);

        // 8x8 info icon
        self::register('info', 8, 8, [
            0b00111100,
            0b01000010,
            0b10011001,
            0b10111001,
            0b10011001,
            0b10000001,
            0b01000010,
            0b00111100,
        ]);

        // 8x8 arrow up
        self::register('arrow_up', 8, 8, [
            0b00011000,
            0b00111100,
            0b01111110,
            0b11111111,
            0b00011000,
            0b00011000,
            0b00011000,
            0b00011000,
        ]);

        // 8x8 arrow down
        self::register('arrow_down', 8, 8, [
            0b00011000,
            0b00011000,
            0b00011000,
            0b00011000,
            0b11111111,
            0b01111110,
            0b00111100,
            0b00011000,
        ]);

        // 8x8 wifi icon
        self::register('wifi', 8, 8, [
            0b00000000,
            0b01111110,
            0b10000001,
            0b00111100,
            0b01000010,
            0b00011000,
            0b00100100,
            0b00011000,
        ]);

        // 8x8 battery icon
        self::register('battery', 8, 8, [
            0b00111100,
            0b01111110,
            0b01111110,
            0b01111110,
            0b01111110,
            0b01111110,
            0b01111110,
            0b00111100,
        ]);
    }

    /**
     * Get bitmap data
     */
    public function getBitmap(): array
    {
        return $this->bitmap;
    }

    /**
     * Get dimensions
     */
    public function getSize(): array
    {
        return ['width' => $this->width, 'height' => $this->height];
    }
}

