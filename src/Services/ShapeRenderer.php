<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Services;

use PhpdaFruit\SSD1306\SSD1306Display;
use PhpdaFruit\SSD1306\Shapes\ProgressBar;
use PhpdaFruit\SSD1306\Shapes\Gauge;
use PhpdaFruit\SSD1306\Shapes\RoundedBox;
use PhpdaFruit\SSD1306\Shapes\Icon;

/**
 * Shape rendering service
 * 
 * Provides high-level shape rendering capabilities including
 * progress bars, gauges, rounded panels, and icons.
 */
class ShapeRenderer
{
    public function __construct(
        private SSD1306Display $display
    ) {}

    /**
     * Render a progress bar
     *
     * @param ProgressBar|array $config ProgressBar object or configuration array
     * @return void
     */
    public function progressBar(ProgressBar|array $config): void
    {
        $bar = $config instanceof ProgressBar ? $config : $this->createProgressBar($config);
        
        // Draw border
        if ($bar->border) {
            $this->display->drawRect($bar->x, $bar->y, $bar->width, $bar->height, 1);
        }
        
        $filledSize = $bar->getFilledSize();
        
        if ($bar->orientation === ProgressBar::ORIENTATION_HORIZONTAL) {
            $this->renderHorizontalProgress($bar, $filledSize);
        } else {
            $this->renderVerticalProgress($bar, $filledSize);
        }
        
        // Show percentage text if requested
        if ($bar->showPercent) {
            $text = sprintf('%d%%', (int)$bar->percent);
            $textX = $bar->x + (int)($bar->width / 2) - (strlen($text) * 3);
            $textY = $bar->y + (int)($bar->height / 2) - 4;
            
            $this->display->setCursor($textX, $textY);
            $this->display->setTextColor(1);
            foreach (str_split($text) as $char) {
                $this->display->write(ord($char));
            }
        }
    }

    /**
     * Render a gauge
     *
     * @param Gauge|array $config Gauge object or configuration array
     * @return void
     */
    public function gauge(Gauge|array $config): void
    {
        $gauge = $config instanceof Gauge ? $config : $this->createGauge($config);
        
        // Draw arc/circle based on style
        if ($gauge->style === Gauge::STYLE_FULL_CIRCLE) {
            $this->display->drawCircle($gauge->cx, $gauge->cy, $gauge->radius, 1);
        } else {
            // Draw arc using multiple lines
            $this->drawArc($gauge->cx, $gauge->cy, $gauge->radius, $gauge->startAngle, $gauge->endAngle);
        }
        
        // Draw ticks
        if ($gauge->showTicks) {
            $this->drawGaugeTicks($gauge);
        }
        
        // Draw needle
        $needle = $gauge->getNeedlePoint();
        $this->display->drawLine($gauge->cx, $gauge->cy, $needle['x'], $needle['y'], 1);
        
        // Draw center dot
        $this->display->fillCircle($gauge->cx, $gauge->cy, 2, 1);
        
        // Show value text if requested
        if ($gauge->showValue) {
            $text = sprintf('%.1f', $gauge->value);
            $textX = $gauge->cx - (strlen($text) * 3);
            $textY = $gauge->cy + $gauge->radius - 8;
            
            $this->display->setCursor($textX, $textY);
            $this->display->setTextColor(1);
            foreach (str_split($text) as $char) {
                $this->display->write(ord($char));
            }
        }
    }

    /**
     * Render a rounded box/panel
     *
     * @param RoundedBox|array $config RoundedBox object or configuration array
     * @return void
     */
    public function roundedPanel(RoundedBox|array $config): void
    {
        $box = $config instanceof RoundedBox ? $config : $this->createRoundedBox($config);
        
        $corners = $box->getCornerCenters();
        
        // Draw the four rounded corners
        foreach ($corners as $corner) {
            if ($box->filled) {
                $this->display->fillCircle($corner['x'], $corner['y'], $box->radius, 1);
            } else if ($box->border) {
                $this->display->drawCircle($corner['x'], $corner['y'], $box->radius, 1);
            }
        }
        
        // Draw connecting lines/rectangles
        if ($box->filled) {
            // Fill the interior
            $this->display->fillRect(
                $box->x + $box->radius,
                $box->y,
                $box->width - $box->radius * 2,
                $box->height,
                1
            );
            $this->display->fillRect(
                $box->x,
                $box->y + $box->radius,
                $box->width,
                $box->height - $box->radius * 2,
                1
            );
        } else if ($box->border) {
            // Draw border lines
            // Top and bottom
            $this->display->drawFastHLine($box->x + $box->radius, $box->y, $box->width - $box->radius * 2, 1);
            $this->display->drawFastHLine($box->x + $box->radius, $box->y + $box->height - 1, $box->width - $box->radius * 2, 1);
            
            // Left and right
            $this->display->drawFastVLine($box->x, $box->y + $box->radius, $box->height - $box->radius * 2, 1);
            $this->display->drawFastVLine($box->x + $box->width - 1, $box->y + $box->radius, $box->height - $box->radius * 2, 1);
        }
    }

    /**
     * Render an icon
     *
     * @param string $name Icon name
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $scale Scale factor (default 1)
     * @return void
     */
    public function icon(string $name, int $x, int $y, int $scale = 1): void
    {
        $icon = Icon::get($name);
        
        if (!$icon) {
            return; // Icon not found
        }
        
        $bitmap = $icon->getBitmap();
        $size = $icon->getSize();
        
        // Draw icon bitmap
        for ($row = 0; $row < $size['height']; $row++) {
            for ($col = 0; $col < $size['width']; $col++) {
                $byte = $bitmap[$row];
                $bit = ($byte >> (7 - $col)) & 1;
                
                if ($bit) {
                    if ($scale === 1) {
                        $this->display->drawPixel($x + $col, $y + $row, 1);
                    } else {
                        // Draw scaled pixel as rectangle
                        $this->display->fillRect(
                            $x + ($col * $scale),
                            $y + ($row * $scale),
                            $scale,
                            $scale,
                            1
                        );
                    }
                }
            }
        }
    }

    /**
     * Render horizontal progress bar fill
     */
    private function renderHorizontalProgress(ProgressBar $bar, int $filledSize): void
    {
        if ($filledSize <= 0) {
            return;
        }
        
        if ($bar->style === ProgressBar::STYLE_SEGMENTED) {
            // Draw segmented bar
            $segmentWidth = (int)(($bar->width - 2) / $bar->segments);
            $filledSegments = (int)(($bar->percent / 100) * $bar->segments);
            
            for ($i = 0; $i < $filledSegments; $i++) {
                $segX = $bar->x + 1 + ($i * $segmentWidth) + ($i > 0 ? 1 : 0);
                $this->display->fillRect($segX, $bar->y + 1, $segmentWidth - 1, $bar->height - 2, 1);
            }
        } else {
            // Draw solid fill
            $this->display->fillRect($bar->x + 1, $bar->y + 1, $filledSize, $bar->height - 2, 1);
        }
    }

    /**
     * Render vertical progress bar fill
     */
    private function renderVerticalProgress(ProgressBar $bar, int $filledSize): void
    {
        if ($filledSize <= 0) {
            return;
        }
        
        // Vertical bars fill from bottom to top
        $fillY = $bar->y + $bar->height - 1 - $filledSize;
        
        if ($bar->style === ProgressBar::STYLE_SEGMENTED) {
            $segmentHeight = (int)(($bar->height - 2) / $bar->segments);
            $filledSegments = (int)(($bar->percent / 100) * $bar->segments);
            
            for ($i = 0; $i < $filledSegments; $i++) {
                $segY = $bar->y + $bar->height - 1 - (($i + 1) * $segmentHeight) + ($i > 0 ? 1 : 0);
                $this->display->fillRect($bar->x + 1, $segY, $bar->width - 2, $segmentHeight - 1, 1);
            }
        } else {
            $this->display->fillRect($bar->x + 1, $fillY, $bar->width - 2, $filledSize, 1);
        }
    }

    /**
     * Draw an arc between two angles
     */
    private function drawArc(int $cx, int $cy, int $radius, float $startAngle, float $endAngle): void
    {
        $steps = 30; // Number of line segments
        $angleStep = ($endAngle - $startAngle) / $steps;
        
        for ($i = 0; $i < $steps; $i++) {
            $angle1 = deg2rad($startAngle + ($i * $angleStep));
            $angle2 = deg2rad($startAngle + (($i + 1) * $angleStep));
            
            $x1 = $cx + (int)($radius * cos($angle1));
            $y1 = $cy + (int)($radius * sin($angle1));
            $x2 = $cx + (int)($radius * cos($angle2));
            $y2 = $cy + (int)($radius * sin($angle2));
            
            $this->display->drawLine($x1, $y1, $x2, $y2, 1);
        }
    }

    /**
     * Draw gauge tick marks
     */
    private function drawGaugeTicks(Gauge $gauge): void
    {
        $angleStep = ($gauge->endAngle - $gauge->startAngle) / ($gauge->tickCount - 1);
        
        for ($i = 0; $i < $gauge->tickCount; $i++) {
            $angle = deg2rad($gauge->startAngle + ($i * $angleStep));
            
            $outerX = $gauge->cx + (int)($gauge->radius * cos($angle));
            $outerY = $gauge->cy + (int)($gauge->radius * sin($angle));
            $innerX = $gauge->cx + (int)(($gauge->radius - 4) * cos($angle));
            $innerY = $gauge->cy + (int)(($gauge->radius - 4) * sin($angle));
            
            $this->display->drawLine($outerX, $outerY, $innerX, $innerY, 1);
        }
    }

    /**
     * Create ProgressBar from array config
     */
    private function createProgressBar(array $config): ProgressBar
    {
        return new ProgressBar(
            $config['x'] ?? 0,
            $config['y'] ?? 0,
            $config['width'] ?? 100,
            $config['height'] ?? 10,
            $config['percent'] ?? 0,
            $config['style'] ?? ProgressBar::STYLE_SOLID,
            $config['orientation'] ?? ProgressBar::ORIENTATION_HORIZONTAL,
            $config['showPercent'] ?? false,
            $config['border'] ?? true,
            $config['segments'] ?? 10,
            $config['cornerRadius'] ?? 2
        );
    }

    /**
     * Create Gauge from array config
     */
    private function createGauge(array $config): Gauge
    {
        return new Gauge(
            $config['cx'] ?? 64,
            $config['cy'] ?? 16,
            $config['radius'] ?? 12,
            $config['value'] ?? 0,
            $config['min'] ?? 0,
            $config['max'] ?? 100,
            $config['style'] ?? Gauge::STYLE_HALF_CIRCLE,
            $config['startAngle'] ?? 180,
            $config['endAngle'] ?? 360,
            $config['showTicks'] ?? true,
            $config['tickCount'] ?? 5,
            $config['showValue'] ?? false
        );
    }

    /**
     * Create RoundedBox from array config
     */
    private function createRoundedBox(array $config): RoundedBox
    {
        return new RoundedBox(
            $config['x'] ?? 0,
            $config['y'] ?? 0,
            $config['width'] ?? 100,
            $config['height'] ?? 30,
            $config['radius'] ?? 4,
            $config['filled'] ?? false,
            $config['border'] ?? true,
            $config['padding'] ?? 2
        );
    }
}

