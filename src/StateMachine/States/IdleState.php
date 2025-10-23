<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\StateMachine\States;

use PhpdaFruit\SSD1306\StateMachine\DisplayState;

/**
 * Idle state example
 * 
 * Displays a simple idle screen with pulsing indicator
 */
class IdleState extends DisplayState
{
    private float $time = 0;

    public function enter(array $context = []): void
    {
        $this->time = 0;
    }

    public function update(float $deltaTime): void
    {
        $this->time += $deltaTime;
    }

    public function exit(): array
    {
        return ['time_in_idle' => $this->time];
    }

    public function render(): void
    {
        // Draw "IDLE" text
        $this->display->setCursor(45, 8);
        $this->display->setTextSize(1);
        $this->display->setTextColor(1);
        foreach (str_split('IDLE') as $char) {
            $this->display->write(ord($char));
        }
        
        // Draw pulsing dot
        $pulse = (int)((sin($this->time * 3) + 1) * 2); // 0-4 range
        if ($pulse > 0) {
            $this->display->fillCircle(64, 24, $pulse, 1);
        }
    }
}

