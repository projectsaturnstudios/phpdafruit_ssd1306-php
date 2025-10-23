<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Services\ShapeRenderer;
use PhpdaFruit\SSD1306\Shapes\ProgressBar;
use PhpdaFruit\SSD1306\Shapes\Gauge;
use PhpdaFruit\SSD1306\Shapes\RoundedBox;
use PhpdaFruit\SSD1306\Shapes\Icon;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

beforeAll(function () {
    Icon::initializeBuiltIns();
});

describe('ShapeRenderer Progress Bar Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause to see output
        }
    });

    it('renders horizontal progress bars at different percentages', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        foreach ([0, 25, 50, 75, 100] as $percent) {
            $display->clearDisplay();
            $bar = ProgressBar::horizontal(10, 10, 100, 8, $percent);
            $renderer->progressBar($bar);
            $display->display();
            usleep(500000); // 0.5s per bar
        }

        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders segmented progress bar', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $bar = new ProgressBar(10, 10, 100, 8, 70, ProgressBar::STYLE_SEGMENTED, segments: 10);
        $renderer->progressBar($bar);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders vertical progress bar', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $bar = ProgressBar::vertical(10, 2, 8, 28, 65);
        $renderer->progressBar($bar);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders progress bar with percentage text', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $bar = new ProgressBar(10, 12, 100, 10, 83, showPercent: true);
        $renderer->progressBar($bar);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('ShapeRenderer Gauge Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('renders gauge at different values', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        foreach ([0, 25, 50, 75, 100] as $value) {
            $display->clearDisplay();
            $gauge = new Gauge(64, 24, 15, $value);
            $renderer->gauge($gauge);
            $display->display();
            usleep(400000); // 0.4s per gauge
        }

        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders gauge with ticks', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $gauge = new Gauge(64, 24, 15, 65, showTicks: true, tickCount: 7);
        $renderer->gauge($gauge);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders full circle gauge', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $gauge = new Gauge(64, 16, 12, 45, style: Gauge::STYLE_FULL_CIRCLE, startAngle: 0, endAngle: 360);
        $renderer->gauge($gauge);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('ShapeRenderer Rounded Box Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('renders rounded panel', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $box = RoundedBox::panel(10, 5, 108, 22);
        $renderer->roundedPanel($box);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders filled rounded box', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $box = new RoundedBox(20, 8, 88, 16, 4, filled: true);
        $renderer->roundedPanel($box);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders button preset', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $button = RoundedBox::button(30, 10, 68, 12);
        $renderer->roundedPanel($button);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('ShapeRenderer Icon Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(800000); // 0.8s pause
        }
    });

    it('renders all built-in icons', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $icons = ['checkmark', 'cross', 'warning', 'info'];
        $positions = [[10, 4], [40, 4], [70, 4], [100, 4]];

        $display->clearDisplay();
        foreach ($icons as $i => $iconName) {
            $renderer->icon($iconName, $positions[$i][0], $positions[$i][1]);
        }
        $display->display();

        expect(count($icons))->toBe(4);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders arrow icons', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $renderer->icon('arrow_up', 40, 4);
        $renderer->icon('arrow_down', 80, 4);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders scaled icon', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        $renderer->icon('checkmark', 50, 4, 2); // 2x scale
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('ShapeRenderer Combined Shapes', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1500000); // 1.5s pause for complex scenes
        }
    });

    it('renders complete dashboard with multiple shapes', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        $display->clearDisplay();
        
        // Panel background
        $panel = RoundedBox::panel(0, 0, 128, 32);
        $renderer->roundedPanel($panel);
        
        // Progress bar
        $bar = ProgressBar::horizontal(10, 22, 50, 6, 65);
        $renderer->progressBar($bar);
        
        // Icons
        $renderer->icon('wifi', 70, 22);
        $renderer->icon('battery', 90, 22);
        
        // Gauge
        $gauge = new Gauge(25, 10, 8, 75, showTicks: false);
        $renderer->gauge($gauge);
        
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('animates progress bar filling', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new ShapeRenderer($display);

        for ($percent = 0; $percent <= 100; $percent += 10) {
            $display->clearDisplay();
            $bar = ProgressBar::horizontal(14, 12, 100, 8, $percent);
            $renderer->progressBar($bar);
            $display->display();
            usleep(100000); // 0.1s per frame
        }

        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

