<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Dashboard layout manager
 * 
 * Grid-based widget positioning with responsive sizing
 * for creating multi-widget dashboard layouts.
 */
class Dashboard
{
    private array $widgets = [];
    private int $rows = 2;
    private int $cols = 2;

    public function __construct(
        private SSD1306Display $display,
        int $rows = 2,
        int $cols = 2
    ) {
        $this->rows = max(1, $rows);
        $this->cols = max(1, $cols);
    }

    /**
     * Add widget to specific grid position
     *
     * @param Widget $widget Widget instance
     * @param int $row Row position (0-indexed)
     * @param int $col Column position (0-indexed)
     * @param int $rowSpan Rows to span
     * @param int $colSpan Columns to span
     * @return self
     */
    public function addWidget(Widget $widget, int $row, int $col, int $rowSpan = 1, int $colSpan = 1): self
    {
        $this->widgets[] = [
            'widget' => $widget,
            'row' => $row,
            'col' => $col,
            'rowSpan' => $rowSpan,
            'colSpan' => $colSpan
        ];
        
        $this->layoutWidget($widget, $row, $col, $rowSpan, $colSpan);
        
        return $this;
    }

    /**
     * Remove all widgets
     *
     * @return self
     */
    public function clearWidgets(): self
    {
        $this->widgets = [];
        return $this;
    }

    /**
     * Get all widgets
     *
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    /**
     * Set grid dimensions
     *
     * @param int $rows Number of rows
     * @param int $cols Number of columns
     * @return self
     */
    public function setGrid(int $rows, int $cols): self
    {
        $this->rows = max(1, $rows);
        $this->cols = max(1, $cols);
        
        // Re-layout existing widgets
        foreach ($this->widgets as $widgetData) {
            $this->layoutWidget(
                $widgetData['widget'],
                $widgetData['row'],
                $widgetData['col'],
                $widgetData['rowSpan'],
                $widgetData['colSpan']
            );
        }
        
        return $this;
    }

    /**
     * Get grid dimensions
     *
     * @return array{rows: int, cols: int}
     */
    public function getGrid(): array
    {
        return ['rows' => $this->rows, 'cols' => $this->cols];
    }

    /**
     * Update all widgets
     *
     * @param float $deltaTime Time since last update
     * @return void
     */
    public function update(float $deltaTime): void
    {
        foreach ($this->widgets as $widgetData) {
            $widgetData['widget']->update($deltaTime);
        }
    }

    /**
     * Render all widgets
     *
     * @return void
     */
    public function render(): void
    {
        foreach ($this->widgets as $widgetData) {
            $widget = $widgetData['widget'];
            
            if ($widget->isVisible()) {
                $widget->render();
            }
        }
    }

    /**
     * Calculate and apply widget bounds based on grid position
     *
     * @param Widget $widget Widget to layout
     * @param int $row Row position
     * @param int $col Column position
     * @param int $rowSpan Rows to span
     * @param int $colSpan Columns to span
     * @return void
     */
    private function layoutWidget(Widget $widget, int $row, int $col, int $rowSpan, int $colSpan): void
    {
        $displayWidth = $this->display->getDisplayWidth();
        $displayHeight = $this->display->getDisplayHeight();
        
        $cellWidth = (int)($displayWidth / $this->cols);
        $cellHeight = (int)($displayHeight / $this->rows);
        
        $x = $col * $cellWidth;
        $y = $row * $cellHeight;
        $width = $colSpan * $cellWidth;
        $height = $rowSpan * $cellHeight;
        
        $widget->setBounds($x, $y, $width, $height);
    }
}

