<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Services;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Animation engine for frame-based animations
 * 
 * Manages animation frames, timing, playback control, and render loops
 * for creating smooth animations on the SSD1306 display.
 */
class AnimationEngine
{
    private array $frames = [];
    private int $currentFrame = 0;
    private bool $isPlaying = false;
    private bool $isPaused = false;
    private bool $loopEnabled = false;
    private mixed $onCompleteCallback = null;
    private float $startTime = 0;
    private float $frameStartTime = 0;

    public function __construct(
        private SSD1306Display $display
    ) {}

    /**
     * Add a frame to the animation sequence
     *
     * @param callable $renderCallback Callback that renders the frame: function(SSD1306Display $display, float $progress)
     * @param int $duration Frame duration in milliseconds
     * @return self
     */
    public function addFrame(callable $renderCallback, int $duration): self
    {
        $this->frames[] = [
            'callback' => $renderCallback,
            'duration' => $duration,
        ];
        
        return $this;
    }

    /**
     * Enable or disable looping
     *
     * @param bool $enabled Whether to loop the animation
     * @return self
     */
    public function loop(bool $enabled = true): self
    {
        $this->loopEnabled = $enabled;
        return $this;
    }

    /**
     * Set callback to execute when animation completes
     *
     * @param callable $callback Callback function
     * @return self
     */
    public function onComplete(callable $callback): self
    {
        $this->onCompleteCallback = $callback;
        return $this;
    }

    /**
     * Play the animation
     *
     * @return void
     */
    public function play(): void
    {
        if (empty($this->frames)) {
            return; // No frames to play
        }

        $this->isPlaying = true;
        $this->isPaused = false;
        $this->startTime = microtime(true);
        $this->frameStartTime = $this->startTime;
        
        $this->runLoop();
    }

    /**
     * Pause the animation
     *
     * @return void
     */
    public function pause(): void
    {
        $this->isPaused = true;
    }

    /**
     * Resume paused animation
     *
     * @return void
     */
    public function resume(): void
    {
        if ($this->isPaused && $this->isPlaying) {
            $this->isPaused = false;
            $this->frameStartTime = microtime(true);
        }
    }

    /**
     * Stop the animation
     *
     * @return void
     */
    public function stop(): void
    {
        $this->isPlaying = false;
        $this->isPaused = false;
    }

    /**
     * Reset animation to beginning
     *
     * @return void
     */
    public function reset(): void
    {
        $this->currentFrame = 0;
        $this->isPlaying = false;
        $this->isPaused = false;
        $this->startTime = 0;
        $this->frameStartTime = 0;
    }

    /**
     * Check if animation is currently playing
     *
     * @return bool
     */
    public function isPlaying(): bool
    {
        return $this->isPlaying && !$this->isPaused;
    }

    /**
     * Check if animation is paused
     *
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->isPaused;
    }

    /**
     * Get current frame index
     *
     * @return int
     */
    public function getCurrentFrame(): int
    {
        return $this->currentFrame;
    }

    /**
     * Get total number of frames
     *
     * @return int
     */
    public function getFrameCount(): int
    {
        return count($this->frames);
    }

    /**
     * Get total animation duration in milliseconds
     *
     * @return int
     */
    public function getTotalDuration(): int
    {
        return array_reduce($this->frames, fn($sum, $frame) => $sum + $frame['duration'], 0);
    }

    /**
     * Clear all frames
     *
     * @return self
     */
    public function clearFrames(): self
    {
        $this->frames = [];
        $this->reset();
        return $this;
    }

    /**
     * Main animation loop
     *
     * @return void
     */
    private function runLoop(): void
    {
        while ($this->isPlaying && !empty($this->frames)) {
            if ($this->isPaused) {
                usleep(10000); // 10ms sleep when paused
                continue;
            }

            $currentTime = microtime(true);
            $elapsed = ($currentTime - $this->frameStartTime) * 1000; // Convert to ms
            
            $frame = $this->frames[$this->currentFrame];
            $progress = min(1.0, $elapsed / $frame['duration']);
            
            // Render current frame
            $this->display->clearDisplay();
            call_user_func($frame['callback'], $this->display, $progress);
            $this->display->display();
            
            // Check if frame is complete
            if ($progress >= 1.0) {
                $this->currentFrame++;
                $this->frameStartTime = $currentTime;
                
                // Check if animation is complete
                if ($this->currentFrame >= count($this->frames)) {
                    if ($this->loopEnabled) {
                        $this->currentFrame = 0;
                    } else {
                        $this->isPlaying = false;
                        
                        // Call completion callback
                        if ($this->onCompleteCallback) {
                            call_user_func($this->onCompleteCallback);
                        }
                        
                        break;
                    }
                }
            }
            
            // Small delay to prevent CPU spinning
            usleep(1000); // 1ms
        }
    }

    /**
     * Play animation synchronously (blocking)
     *
     * @return void
     */
    public function playSync(): void
    {
        $this->play();
    }

    /**
     * Render a single frame by index
     *
     * @param int $frameIndex Frame index to render
     * @param float $progress Progress within frame (0.0 to 1.0)
     * @return void
     */
    public function renderFrame(int $frameIndex, float $progress = 1.0): void
    {
        if (!isset($this->frames[$frameIndex])) {
            return;
        }

        $frame = $this->frames[$frameIndex];
        $this->display->clearDisplay();
        call_user_func($frame['callback'], $this->display, $progress);
        $this->display->display();
    }

    /**
     * Get animation progress (0.0 to 1.0)
     *
     * @return float
     */
    public function getProgress(): float
    {
        if (empty($this->frames) || !$this->isPlaying) {
            return 0.0;
        }

        $totalDuration = $this->getTotalDuration();
        if ($totalDuration === 0) {
            return 1.0;
        }

        $elapsed = (microtime(true) - $this->startTime) * 1000;
        return min(1.0, $elapsed / $totalDuration);
    }

    /**
     * Create a simple fade animation
     *
     * @param callable $renderCallback Callback to render content
     * @param int $duration Total fade duration in ms
     * @param bool $fadeIn True for fade in, false for fade out
     * @return self New animation engine instance
     */
    public static function fade(SSD1306Display $display, callable $renderCallback, int $duration, bool $fadeIn = true): self
    {
        $engine = new self($display);
        $steps = 10;
        $frameDuration = (int)($duration / $steps);
        
        for ($i = 0; $i <= $steps; $i++) {
            $opacity = $fadeIn ? ($i / $steps) : (1 - $i / $steps);
            
            $engine->addFrame(function($disp, $progress) use ($renderCallback, $opacity) {
                call_user_func($renderCallback, $disp);
                // Opacity simulation by dithering would go here
            }, $frameDuration);
        }
        
        return $engine;
    }

    /**
     * Create a simple slide animation
     *
     * @param callable $renderCallback Callback to render content
     * @param int $duration Slide duration in ms
     * @param string $direction Direction: 'left', 'right', 'up', 'down'
     * @return self New animation engine instance
     */
    public static function slide(SSD1306Display $display, callable $renderCallback, int $duration, string $direction = 'left'): self
    {
        $engine = new self($display);
        $steps = 20;
        $frameDuration = (int)($duration / $steps);
        $width = $display->getDisplayWidth();
        $height = $display->getDisplayHeight();
        
        for ($i = 0; $i <= $steps; $i++) {
            $progress = $i / $steps;
            
            $engine->addFrame(function($disp, $prog) use ($renderCallback, $progress, $direction, $width, $height) {
                $offset = (int)(($progress) * ($direction === 'left' || $direction === 'right' ? $width : $height));
                
                // Apply offset and render
                // This is simplified - full implementation would handle actual translation
                call_user_func($renderCallback, $disp);
            }, $frameDuration);
        }
        
        return $engine;
    }
}

