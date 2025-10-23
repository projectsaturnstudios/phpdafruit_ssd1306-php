<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Services\ShapeRenderer;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\Shapes\ProgressBar;
use PhpdaFruit\SSD1306\Shapes\Gauge;
use PhpdaFruit\SSD1306\Shapes\RoundedBox;
use PhpdaFruit\SSD1306\Shapes\Icon;

beforeAll(function () {
    Icon::initializeBuiltIns();
});

describe('ShapeRenderer Construction', function () {
    it('creates renderer with display instance', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect($renderer)->toBeInstanceOf(ShapeRenderer::class);
    });
});

describe('ShapeRenderer Progress Bar', function () {
    it('renders progress bar from object', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $bar = new ProgressBar(0, 0, 100, 10, 50);
        
        expect(function () use ($renderer, $bar) {
            $renderer->progressBar($bar);
        })->not->toThrow(Exception::class);
    });

    it('renders progress bar from array config', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->progressBar([
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 10,
                'percent' => 75
            ]);
        })->not->toThrow(Exception::class);
    });

    it('renders horizontal progress bar', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $bar = ProgressBar::horizontal(10, 10, 100, 8, 60);
        
        expect(function () use ($renderer, $bar) {
            $renderer->progressBar($bar);
        })->not->toThrow(Exception::class);
    });

    it('renders vertical progress bar', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $bar = ProgressBar::vertical(10, 10, 8, 100, 40);
        
        expect(function () use ($renderer, $bar) {
            $renderer->progressBar($bar);
        })->not->toThrow(Exception::class);
    });

    it('renders segmented progress bar', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $bar = new ProgressBar(0, 0, 100, 10, 50, ProgressBar::STYLE_SEGMENTED);
        
        expect(function () use ($renderer, $bar) {
            $renderer->progressBar($bar);
        })->not->toThrow(Exception::class);
    });
});

describe('ShapeRenderer Gauge', function () {
    it('renders gauge from object', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $gauge = new Gauge(64, 16, 12, 50);
        
        expect(function () use ($renderer, $gauge) {
            $renderer->gauge($gauge);
        })->not->toThrow(Exception::class);
    });

    it('renders gauge from array config', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->gauge([
                'cx' => 64,
                'cy' => 16,
                'radius' => 12,
                'value' => 75,
                'showTicks' => true
            ]);
        })->not->toThrow(Exception::class);
    });

    it('renders full circle gauge', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $gauge = new Gauge(64, 16, 12, 50, style: Gauge::STYLE_FULL_CIRCLE);
        
        expect(function () use ($renderer, $gauge) {
            $renderer->gauge($gauge);
        })->not->toThrow(Exception::class);
    });

    it('renders half circle gauge', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $gauge = new Gauge(64, 16, 12, 50, style: Gauge::STYLE_HALF_CIRCLE);
        
        expect(function () use ($renderer, $gauge) {
            $renderer->gauge($gauge);
        })->not->toThrow(Exception::class);
    });
});

describe('ShapeRenderer Rounded Box', function () {
    it('renders rounded box from object', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $box = new RoundedBox(0, 0, 100, 30);
        
        expect(function () use ($renderer, $box) {
            $renderer->roundedPanel($box);
        })->not->toThrow(Exception::class);
    });

    it('renders rounded box from array config', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->roundedPanel([
                'x' => 10,
                'y' => 10,
                'width' => 100,
                'height' => 30,
                'radius' => 5
            ]);
        })->not->toThrow(Exception::class);
    });

    it('renders filled rounded box', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $box = new RoundedBox(0, 0, 100, 30, filled: true);
        
        expect(function () use ($renderer, $box) {
            $renderer->roundedPanel($box);
        })->not->toThrow(Exception::class);
    });

    it('renders panel preset', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        $panel = RoundedBox::panel(10, 10, 100, 40);
        
        expect(function () use ($renderer, $panel) {
            $renderer->roundedPanel($panel);
        })->not->toThrow(Exception::class);
    });
});

describe('ShapeRenderer Icon', function () {
    it('renders existing icon', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->icon('checkmark', 10, 10);
        })->not->toThrow(Exception::class);
    });

    it('handles non-existent icon gracefully', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->icon('non_existent_icon', 10, 10);
        })->not->toThrow(Exception::class);
    });

    it('renders scaled icon', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->icon('checkmark', 10, 10, 2);
        })->not->toThrow(Exception::class);
    });

    it('renders all built-in icons', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new ShapeRenderer($display);
        
        foreach (['checkmark', 'cross', 'warning', 'info', 'arrow_up', 'arrow_down', 'wifi', 'battery'] as $iconName) {
            expect(function () use ($renderer, $iconName) {
                $renderer->icon($iconName, 0, 0);
            })->not->toThrow(Exception::class);
        }
    });
});

