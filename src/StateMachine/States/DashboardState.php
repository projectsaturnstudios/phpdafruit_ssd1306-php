<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\StateMachine\States;

use PhpdaFruit\SSD1306\StateMachine\DisplayState;

/**
 * Dashboard state example
 * 
 * Displays a simple dashboard with stats
 */
class DashboardState extends DisplayState
{
    private float $time = 0;
    private int $value1 = 0;
    private int $value2 = 0;

    public function enter(array $context = []): void
    {
        $this->time = 0;
        $this->value1 = $context['value1'] ?? 75;
        $this->value2 = $context['value2'] ?? 50;
    }

    public function update(float $deltaTime): void
    {
        $this->time += $deltaTime;
        
        // Simulate value changes
        $this->value1 = (int)(75 + sin($this->time) * 10);
        $this->value2 = (int)(50 + cos($this->time * 1.5) * 15);
    }

    public function exit(): array
    {
        return [
            'last_value1' => $this->value1,
            'last_value2' => $this->value2
        ];
    }

    public function render(): void
    {
        // Title
        $this->display->setCursor(35, 2);
        $this->display->setTextSize(1);
        $this->display->setTextColor(1);
        foreach (str_split('DASHBOARD') as $char) {
            $this->display->write(ord($char));
        }
        
        // Progress bar 1
        $this->display->drawRect(5, 12, 55, 6, 1);
        $filledWidth = (int)((53 / 100) * $this->value1);
        if ($filledWidth > 0) {
            $this->display->fillRect(6, 13, $filledWidth, 4, 1);
        }
        
        // Progress bar 2
        $this->display->drawRect(68, 12, 55, 6, 1);
        $filledWidth2 = (int)((53 / 100) * $this->value2);
        if ($filledWidth2 > 0) {
            $this->display->fillRect(69, 13, $filledWidth2, 4, 1);
        }
        
        // Values
        $this->display->setCursor(20, 22);
        $this->display->setTextSize(1);
        foreach (str_split((string)$this->value1) as $char) {
            $this->display->write(ord($char));
        }
        
        $this->display->setCursor(85, 22);
        foreach (str_split((string)$this->value2) as $char) {
            $this->display->write(ord($char));
        }
    }
}

