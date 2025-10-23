<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Concerns;

use PhpdaFruit\SSD1306\Services\AnimationEngine;

/**
 * Trait for adding animation support to any class
 * 
 * Provides methods to attach and control animations
 * for components that need animated behavior.
 */
trait HasAnimations
{
    private ?AnimationEngine $animation = null;
    private bool $isAnimating = false;

    /**
     * Set animation for this component
     *
     * @param AnimationEngine $animation Animation engine instance
     * @return self
     */
    public function setAnimation(AnimationEngine $animation): self
    {
        $this->animation = $animation;
        return $this;
    }

    /**
     * Start animation
     *
     * @return self
     */
    public function startAnimation(): self
    {
        if ($this->animation) {
            $this->isAnimating = true;
            $this->animation->play();
        }
        
        return $this;
    }

    /**
     * Stop animation
     *
     * @return self
     */
    public function stopAnimation(): self
    {
        if ($this->animation) {
            $this->isAnimating = false;
            $this->animation->stop();
        }
        
        return $this;
    }

    /**
     * Pause animation
     *
     * @return self
     */
    public function pauseAnimation(): self
    {
        if ($this->animation) {
            $this->animation->pause();
        }
        
        return $this;
    }

    /**
     * Resume animation
     *
     * @return self
     */
    public function resumeAnimation(): self
    {
        if ($this->animation) {
            $this->animation->resume();
        }
        
        return $this;
    }

    /**
     * Reset animation
     *
     * @return self
     */
    public function resetAnimation(): self
    {
        if ($this->animation) {
            $this->animation->reset();
        }
        
        return $this;
    }

    /**
     * Check if component is currently animating
     *
     * @return bool
     */
    public function isAnimating(): bool
    {
        return $this->isAnimating && $this->animation && $this->animation->isPlaying();
    }

    /**
     * Get current animation
     *
     * @return AnimationEngine|null
     */
    public function getAnimation(): ?AnimationEngine
    {
        return $this->animation;
    }

    /**
     * Check if animation is set
     *
     * @return bool
     */
    public function hasAnimation(): bool
    {
        return $this->animation !== null;
    }

    /**
     * Remove animation
     *
     * @return self
     */
    public function clearAnimation(): self
    {
        $this->stopAnimation();
        $this->animation = null;
        
        return $this;
    }

    /**
     * Create and set a simple animation
     *
     * @param callable $callback Frame render callback
     * @param int $duration Duration in milliseconds
     * @param bool $loop Whether to loop
     * @return self
     */
    public function animate(callable $callback, int $duration, bool $loop = false): self
    {
        if (!isset($this->display)) {
            throw new \RuntimeException('Display instance not available for animation');
        }

        $animation = new AnimationEngine($this->display);
        $animation->addFrame($callback, $duration);
        
        if ($loop) {
            $animation->loop(true);
        }
        
        $this->setAnimation($animation);
        
        return $this;
    }
}

