<?php
declare(strict_types=1);
use PhpdaFruit\SSD1306\UI\Dashboard;
use PhpdaFruit\SSD1306\UI\Widgets\TextWidget;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('Dashboard Construction', function () {
    it('creates dashboard with grid', function () {
        $display = DisplayFactory::forTesting();
        $dashboard = new Dashboard($display, 2, 2);
        
        $grid = $dashboard->getGrid();
        
        expect($grid['rows'])->toBe(2)
            ->and($grid['cols'])->toBe(2);
    });
});

describe('Dashboard Widget Management', function () {
    it('adds widgets', function () {
        $display = DisplayFactory::forTesting();
        $dashboard = new Dashboard($display);
        $widget = new TextWidget($display);
        
        $dashboard->addWidget($widget, 0, 0);
        
        expect($dashboard->getWidgets())->toHaveCount(1);
    });

    it('clears widgets', function () {
        $display = DisplayFactory::forTesting();
        $dashboard = new Dashboard($display);
        $widget = new TextWidget($display);
        
        $dashboard->addWidget($widget, 0, 0)->clearWidgets();
        
        expect($dashboard->getWidgets())->toHaveCount(0);
    });

    it('layouts widgets in grid', function () {
        $display = DisplayFactory::forTesting(128, 32);
        $dashboard = new Dashboard($display, 2, 2);
        $widget = new TextWidget($display);
        
        $dashboard->addWidget($widget, 0, 1); // Top right
        $bounds = $widget->getBounds();
        
        expect($bounds['x'])->toBe(64) // Half of 128
            ->and($bounds['y'])->toBe(0);
    });
});

