<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Services\AnimationEngine;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('AnimationEngine Simple Animations', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause after animation
        }
    });

    it('renders bouncing ball animation', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        // Create bouncing ball animation (5 frames)
        $positions = [
            ['x' => 10, 'y' => 4],
            ['x' => 20, 'y' => 8],
            ['x' => 30, 'y' => 12],
            ['x' => 40, 'y' => 8],
            ['x' => 50, 'y' => 4],
        ];

        foreach ($positions as $pos) {
            $engine->addFrame(function($disp, $progress) use ($pos) {
                $disp->fillCircle($pos['x'], $pos['y'], 3, 1);
            }, 200); // 200ms per frame
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(5);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders moving rectangle', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        // Rectangle sliding across screen
        for ($x = 0; $x <= 100; $x += 20) {
            $engine->addFrame(function($disp, $progress) use ($x) {
                $disp->fillRect($x, 10, 20, 10, 1);
            }, 150);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(6);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders progress animation', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        // Progress bar filling
        for ($percent = 0; $percent <= 100; $percent += 25) {
            $engine->addFrame(function($disp, $progress) use ($percent) {
                $width = (int)((100 / 100) * $percent);
                $disp->drawRect(10, 10, 100, 10, 1);
                $disp->fillRect(11, 11, $width, 8, 1);
            }, 200);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(5);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('AnimationEngine Text Animations', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('renders text sliding in', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        $text = 'Hello!';
        
        for ($x = 128; $x >= 30; $x -= 20) {
            $engine->addFrame(function($disp, $progress) use ($text, $x) {
                $disp->setCursor($x, 12);
                $disp->setTextColor(1);
                foreach (str_split($text) as $char) {
                    $disp->write(ord($char));
                }
            }, 100);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBeGreaterThan(0);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders blinking text', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        $text = 'BLINK';
        
        // Alternate between visible and invisible
        for ($i = 0; $i < 6; $i++) {
            $visible = $i % 2 === 0;
            $engine->addFrame(function($disp, $progress) use ($text, $visible) {
                if ($visible) {
                    $disp->setCursor(40, 12);
                    $disp->setTextColor(1);
                    foreach (str_split($text) as $char) {
                        $disp->write(ord($char));
                    }
                }
            }, 250);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(6);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('AnimationEngine Loop Control', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(300000); // 0.3s pause
        }
    });

    it('loops animation twice', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        // Simple 3-frame animation
        for ($x = 10; $x <= 30; $x += 10) {
            $engine->addFrame(function($disp, $progress) use ($x) {
                $disp->fillCircle($x, 16, 3, 1);
            }, 150);
        }

        // Play twice by not looping but manually restarting
        $engine->play();
        
        usleep(550000); // Wait for first play
        
        $engine->reset();
        $engine->play();

        expect($engine->getFrameCount())->toBe(3);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('AnimationEngine Static Factories', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(800000); // 0.8s pause
        }
    });

    it('renders fade animation', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        
        $engine = AnimationEngine::fade(
            $display,
            function($disp) {
                $disp->setCursor(30, 12);
                $disp->setTextColor(1);
                foreach (str_split('FADE') as $char) {
                    $disp->write(ord($char));
                }
            },
            600,
            true
        );

        $engine->play();

        expect($engine->getFrameCount())->toBeGreaterThan(0);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders slide animation', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        
        $engine = AnimationEngine::slide(
            $display,
            function($disp) {
                $disp->fillRect(40, 8, 48, 16, 1);
            },
            600,
            'left'
        );

        $engine->play();

        expect($engine->getFrameCount())->toBeGreaterThan(0);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('AnimationEngine Complex Sequences', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause for complex animations
        }
    });

    it('renders multi-shape animation', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        // Circle growing
        for ($r = 2; $r <= 10; $r += 2) {
            $engine->addFrame(function($disp, $progress) use ($r) {
                $disp->drawCircle(30, 16, $r, 1);
            }, 150);
        }

        // Rectangle moving
        for ($x = 50; $x <= 90; $x += 20) {
            $engine->addFrame(function($disp, $progress) use ($x) {
                $disp->fillRect($x, 12, 10, 8, 1);
            }, 150);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(8); // 5 circle frames + 3 rectangle frames
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders spinning line', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        $cx = 64;
        $cy = 16;
        $length = 12;

        // Rotate line 360 degrees
        for ($angle = 0; $angle < 360; $angle += 30) {
            $engine->addFrame(function($disp, $progress) use ($cx, $cy, $length, $angle) {
                $rad = deg2rad($angle);
                $x2 = $cx + (int)($length * cos($rad));
                $y2 = $cy + (int)($length * sin($rad));
                
                $disp->drawLine($cx, $cy, $x2, $y2, 1);
                $disp->fillCircle($cx, $cy, 2, 1);
            }, 80);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(12);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders countdown timer', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $engine = new AnimationEngine($display);

        // Countdown from 5 to 1
        for ($num = 5; $num >= 1; $num--) {
            $engine->addFrame(function($disp, $progress) use ($num) {
                $disp->setCursor(60, 10);
                $disp->setTextSize(2);
                $disp->setTextColor(1);
                $disp->write(ord((string)$num));
            }, 300);
        }

        $engine->play();

        expect($engine->getFrameCount())->toBe(5);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

