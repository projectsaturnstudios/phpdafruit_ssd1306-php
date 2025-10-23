<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Notification/toast system
 * 
 * Temporary notifications with auto-dismiss,
 * slide-in animations, and priority levels.
 */
class Notification
{
    public const PRIORITY_INFO = 'info';
    public const PRIORITY_WARNING = 'warning';
    public const PRIORITY_ERROR = 'error';

    private float $showTime = 0;
    private bool $active = false;
    private float $slideProgress = 0;

    public function __construct(
        private SSD1306Display $display,
        private string $message,
        private string $priority = self::PRIORITY_INFO,
        private float $duration = 3.0
    ) {}

    /**
     * Show the notification
     *
     * @return void
     */
    public function show(): void
    {
        $this->active = true;
        $this->showTime = microtime(true);
        $this->slideProgress = 0;
    }

    /**
     * Dismiss the notification
     *
     * @return void
     */
    public function dismiss(): void
    {
        $this->active = false;
    }

    /**
     * Update notification state
     *
     * @param float $deltaTime Time since last update
     * @return void
     */
    public function update(float $deltaTime): void
    {
        if (!$this->active) {
            return;
        }

        $elapsed = microtime(true) - $this->showTime;
        
        // Slide in animation (first 0.3 seconds)
        if ($elapsed < 0.3) {
            $this->slideProgress = $elapsed / 0.3;
        } else {
            $this->slideProgress = 1.0;
        }
        
        // Auto-dismiss after duration
        if ($elapsed >= $this->duration) {
            $this->dismiss();
        }
    }

    /**
     * Check if notification is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Render the notification
     *
     * @return void
     */
    public function render(): void
    {
        if (!$this->active) {
            return;
        }

        $width = $this->display->getDisplayWidth();
        $height = 12;
        $y = (int)((1 - $this->slideProgress) * -$height); // Slide from top
        
        // Draw background box
        $this->display->fillRect(0, $y, $width, $height, 1);
        
        // Draw border for different priorities
        if ($this->priority === self::PRIORITY_WARNING || $this->priority === self::PRIORITY_ERROR) {
            $this->display->drawRect(0, $y, $width, $height, 0); // Invert border
        }
        
        // Draw message
        $textX = 4;
        $textY = $y + 2;
        $this->display->setCursor($textX, $textY);
        $this->display->setTextSize(1);
        $this->display->setTextColor(0); // Black on white background
        
        // Truncate message if too long
        $maxChars = (int)(($width - 8) / 6); // Approximate char width
        $displayMessage = strlen($this->message) > $maxChars 
            ? substr($this->message, 0, $maxChars - 3) . '...'
            : $this->message;
        
        foreach (str_split($displayMessage) as $char) {
            $this->display->write(ord($char));
        }
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Get duration
     *
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * Create info notification
     */
    public static function info(SSD1306Display $display, string $message, float $duration = 3.0): self
    {
        return new self($display, $message, self::PRIORITY_INFO, $duration);
    }

    /**
     * Create warning notification
     */
    public static function warning(SSD1306Display $display, string $message, float $duration = 4.0): self
    {
        return new self($display, $message, self::PRIORITY_WARNING, $duration);
    }

    /**
     * Create error notification
     */
    public static function error(SSD1306Display $display, string $message, float $duration = 5.0): self
    {
        return new self($display, $message, self::PRIORITY_ERROR, $duration);
    }
}

