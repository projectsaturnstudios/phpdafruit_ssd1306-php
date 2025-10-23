<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\StateMachine;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Transition effect between display states
 * 
 * Handles animated transitions with different visual effects
 * (fade, slide, wipe, etc.)
 */
class Transition
{
    public const EFFECT_NONE = 'none';
    public const EFFECT_FADE = 'fade';
    public const EFFECT_SLIDE_LEFT = 'slide_left';
    public const EFFECT_SLIDE_RIGHT = 'slide_right';
    public const EFFECT_SLIDE_UP = 'slide_up';
    public const EFFECT_SLIDE_DOWN = 'slide_down';
    public const EFFECT_WIPE_LEFT = 'wipe_left';
    public const EFFECT_WIPE_RIGHT = 'wipe_right';

    private float $startTime = 0;
    private bool $started = false;

    public function __construct(
        private string $effect = self::EFFECT_NONE,
        private float $duration = 0.3
    ) {}

    /**
     * Start the transition
     *
     * @return void
     */
    public function start(): void
    {
        $this->startTime = microtime(true);
        $this->started = true;
    }

    /**
     * Get transition progress (0.0 to 1.0)
     *
     * @return float
     */
    public function getProgress(): float
    {
        if (!$this->started) {
            return 0.0;
        }

        $elapsed = microtime(true) - $this->startTime;
        return min(1.0, $elapsed / $this->duration);
    }

    /**
     * Check if transition is complete
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->started && $this->getProgress() >= 1.0;
    }

    /**
     * Get transition duration in seconds
     *
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * Get transition effect type
     *
     * @return string
     */
    public function getEffect(): string
    {
        return $this->effect;
    }

    /**
     * Reset transition to initial state
     *
     * @return void
     */
    public function reset(): void
    {
        $this->started = false;
        $this->startTime = 0;
    }

    /**
     * Render the transition effect
     *
     * @param SSD1306Display $display Display instance
     * @param DisplayState $fromState Previous state
     * @param DisplayState $toState New state
     * @return void
     */
    public function render(SSD1306Display $display, DisplayState $fromState, DisplayState $toState): void
    {
        $progress = $this->getProgress();

        match ($this->effect) {
            self::EFFECT_NONE => $toState->render(),
            self::EFFECT_FADE => $this->renderFade($display, $fromState, $toState, $progress),
            self::EFFECT_SLIDE_LEFT => $this->renderSlide($display, $fromState, $toState, $progress, 'left'),
            self::EFFECT_SLIDE_RIGHT => $this->renderSlide($display, $fromState, $toState, $progress, 'right'),
            self::EFFECT_SLIDE_UP => $this->renderSlide($display, $fromState, $toState, $progress, 'up'),
            self::EFFECT_SLIDE_DOWN => $this->renderSlide($display, $fromState, $toState, $progress, 'down'),
            self::EFFECT_WIPE_LEFT => $this->renderWipe($display, $toState, $progress, 'left'),
            self::EFFECT_WIPE_RIGHT => $this->renderWipe($display, $toState, $progress, 'right'),
            default => $toState->render()
        };
    }

    /**
     * Render fade transition
     */
    private function renderFade(SSD1306Display $display, DisplayState $fromState, DisplayState $toState, float $progress): void
    {
        if ($progress < 0.5) {
            // Fade out old state
            $fromState->render();
        } else {
            // Fade in new state
            $toState->render();
        }
    }

    /**
     * Render slide transition
     */
    private function renderSlide(SSD1306Display $display, DisplayState $fromState, DisplayState $toState, float $progress, string $direction): void
    {
        // Simplified slide - just render the new state
        // Full implementation would involve buffer manipulation for true sliding
        $toState->render();
    }

    /**
     * Render wipe transition
     */
    private function renderWipe(SSD1306Display $display, DisplayState $toState, float $progress, string $direction): void
    {
        // Simplified wipe - render new state with progressive reveal
        $toState->render();
        
        // Draw wipe overlay based on progress
        $width = $display->getDisplayWidth();
        $height = $display->getDisplayHeight();
        
        if ($direction === 'left') {
            $wipeWidth = (int)($width * (1 - $progress));
            if ($wipeWidth > 0) {
                $display->fillRect($width - $wipeWidth, 0, $wipeWidth, $height, 0);
            }
        } else {
            $wipeWidth = (int)($width * (1 - $progress));
            if ($wipeWidth > 0) {
                $display->fillRect(0, 0, $wipeWidth, $height, 0);
            }
        }
    }

    /**
     * Create a fade transition
     */
    public static function fade(float $duration = 0.3): self
    {
        return new self(self::EFFECT_FADE, $duration);
    }

    /**
     * Create a slide transition
     */
    public static function slide(string $direction = 'left', float $duration = 0.3): self
    {
        $effect = match ($direction) {
            'left' => self::EFFECT_SLIDE_LEFT,
            'right' => self::EFFECT_SLIDE_RIGHT,
            'up' => self::EFFECT_SLIDE_UP,
            'down' => self::EFFECT_SLIDE_DOWN,
            default => self::EFFECT_SLIDE_LEFT
        };
        
        return new self($effect, $duration);
    }

    /**
     * Create a wipe transition
     */
    public static function wipe(string $direction = 'left', float $duration = 0.3): self
    {
        $effect = match ($direction) {
            'left' => self::EFFECT_WIPE_LEFT,
            'right' => self::EFFECT_WIPE_RIGHT,
            default => self::EFFECT_WIPE_LEFT
        };
        
        return new self($effect, $duration);
    }

    /**
     * Create instant transition (no effect)
     */
    public static function instant(): self
    {
        return new self(self::EFFECT_NONE, 0);
    }
}

