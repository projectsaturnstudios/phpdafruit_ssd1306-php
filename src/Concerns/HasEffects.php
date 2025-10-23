<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Concerns;

use PhpdaFruit\SSD1306\Effects\TextEffect;

/**
 * Trait for adding effect support to any class
 * 
 * Provides methods to apply and manage visual effects
 * (fade, scroll, typewriter, etc.) to components.
 */
trait HasEffects
{
    private array $effects = [];
    private bool $effectsEnabled = true;

    /**
     * Add an effect to this component
     *
     * @param string $name Effect identifier
     * @param TextEffect $effect Effect instance
     * @return self
     */
    public function addEffect(string $name, TextEffect $effect): self
    {
        $this->effects[$name] = $effect;
        return $this;
    }

    /**
     * Remove an effect
     *
     * @param string $name Effect identifier
     * @return self
     */
    public function removeEffect(string $name): self
    {
        unset($this->effects[$name]);
        return $this;
    }

    /**
     * Get an effect by name
     *
     * @param string $name Effect identifier
     * @return TextEffect|null
     */
    public function getEffect(string $name): ?TextEffect
    {
        return $this->effects[$name] ?? null;
    }

    /**
     * Check if effect exists
     *
     * @param string $name Effect identifier
     * @return bool
     */
    public function hasEffect(string $name): bool
    {
        return isset($this->effects[$name]);
    }

    /**
     * Get all effects
     *
     * @return array<string, TextEffect>
     */
    public function getEffects(): array
    {
        return $this->effects;
    }

    /**
     * Clear all effects
     *
     * @return self
     */
    public function clearEffects(): self
    {
        $this->effects = [];
        return $this;
    }

    /**
     * Enable effects
     *
     * @return self
     */
    public function enableEffects(): self
    {
        $this->effectsEnabled = true;
        return $this;
    }

    /**
     * Disable effects
     *
     * @return self
     */
    public function disableEffects(): self
    {
        $this->effectsEnabled = false;
        return $this;
    }

    /**
     * Check if effects are enabled
     *
     * @return bool
     */
    public function areEffectsEnabled(): bool
    {
        return $this->effectsEnabled;
    }

    /**
     * Apply an effect with fluent interface
     *
     * @param TextEffect $effect Effect to apply
     * @param string|null $name Optional name (auto-generated if null)
     * @return self
     */
    public function withEffect(TextEffect $effect, ?string $name = null): self
    {
        $name = $name ?? get_class($effect) . '_' . uniqid();
        $this->addEffect($name, $effect);
        return $this;
    }

    /**
     * Apply all effects to render
     *
     * @param string $text Text to render with effects
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param float $progress Effect progress (0.0 to 1.0)
     * @return void
     */
    public function applyEffects(string $text, int $x, int $y, float $progress = 1.0): void
    {
        if (!$this->effectsEnabled || empty($this->effects)) {
            return;
        }

        if (!isset($this->display)) {
            throw new \RuntimeException('Display instance not available for effects');
        }

        foreach ($this->effects as $effect) {
            $effect->render($this->display, $text, $x, $y, $progress);
        }
    }

    /**
     * Reset all effects
     *
     * @return self
     */
    public function resetEffects(): self
    {
        foreach ($this->effects as $effect) {
            $effect->reset();
        }
        
        return $this;
    }

    /**
     * Get effect count
     *
     * @return int
     */
    public function getEffectCount(): int
    {
        return count($this->effects);
    }
}

