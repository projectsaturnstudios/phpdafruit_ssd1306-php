<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\StateMachine\States;

use PhpdaFruit\SSD1306\StateMachine\DisplayState;

/**
 * Alert state example
 * 
 * Displays a blinking alert message
 */
class AlertState extends DisplayState
{
    private float $time = 0;
    private string $message = 'ALERT';

    public function enter(array $context = []): void
    {
        $this->time = 0;
        $this->message = $context['message'] ?? 'ALERT';
    }

    public function update(float $deltaTime): void
    {
        $this->time += $deltaTime;
    }

    public function exit(): array
    {
        return ['alert_duration' => $this->time];
    }

    public function render(): void
    {
        // Blink effect (2 blinks per second)
        $visible = ((int)($this->time * 2) % 2) === 0;
        
        if ($visible) {
            // Draw border
            $this->display->drawRect(5, 5, 118, 22, 1);
            $this->display->drawRect(6, 6, 116, 20, 1);
            
            // Draw alert text
            $textX = 64 - (strlen($this->message) * 3);
            $this->display->setCursor($textX, 12);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            foreach (str_split($this->message) as $char) {
                $this->display->write(ord($char));
            }
        }
    }
}

