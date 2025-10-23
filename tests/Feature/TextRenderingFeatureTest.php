<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Services\TextRenderer;
use PhpdaFruit\SSD1306\Effects\ScrollingText;
use PhpdaFruit\SSD1306\Effects\TypewriterText;
use PhpdaFruit\SSD1306\Effects\MarqueeText;
use PhpdaFruit\SSD1306\Effects\FadeText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('TextRenderer Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause to see output
        }
    });

    it('renders basic text', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);

        $display->clearDisplay();
        $renderer->text('Hello', 0, 0);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders centered text', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);

        $display->clearDisplay();
        $renderer->centeredText('Center', 12);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders text with size', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);

        $display->clearDisplay();
        $renderer->text('Big', 0, 0, ['size' => 2]);
        $display->display();

        expect($display->hasBuffer())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders wrapped text', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);

        $display->clearDisplay();
        $lineCount = $renderer->wrappedText('This text should wrap to multiple lines', 0, 0, 80);
        $display->display();

        expect($lineCount)->toBeGreaterThan(1);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('ScrollingText Effect Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('scrolls text from right to left', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);
        $effect = new ScrollingText(ScrollingText::DIRECTION_LEFT, 128);

        // Animate the scroll
        for ($i = 0; $i <= 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $renderer->applyEffect($effect, 'Scroll', 0, 12, $progress);
            $display->display();
            usleep(50000); // 50ms between frames
        }

        expect($effect->isComplete(1.0))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('scrolls in all directions', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);

        $directions = [
            ['dir' => ScrollingText::DIRECTION_LEFT, 'text' => 'Left'],
            ['dir' => ScrollingText::DIRECTION_RIGHT, 'text' => 'Right'],
            ['dir' => ScrollingText::DIRECTION_UP, 'text' => 'Up'],
            ['dir' => ScrollingText::DIRECTION_DOWN, 'text' => 'Down'],
        ];

        foreach ($directions as $test) {
            $effect = new ScrollingText($test['dir'], 50);
            
            for ($i = 0; $i <= 5; $i++) {
                $progress = $i / 5;
                $display->clearDisplay();
                $renderer->applyEffect($effect, $test['text'], 40, 12, $progress);
                $display->display();
                usleep(100000); // 100ms
            }
        }

        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('TypewriterText Effect Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('types text character by character', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);
        $effect = new TypewriterText(true);

        // Animate typewriter effect
        for ($i = 0; $i <= 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $renderer->applyEffect($effect, 'Type!', 0, 12, $progress);
            $display->display();
            usleep(100000); // 100ms per step
        }

        expect($effect->isComplete(1.0))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('MarqueeText Effect Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('scrolls text in continuous loop', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);
        $effect = new MarqueeText(2, 20);

        // Show marquee for 1.5 loops
        for ($i = 0; $i <= 15; $i++) {
            $progress = $i / 10; // Goes from 0 to 1.5
            $display->clearDisplay();
            $renderer->applyEffect($effect, 'Scrolling...', 0, 12, $progress);
            $display->display();
            usleep(100000); // 100ms per frame
        }

        expect($effect->isComplete(1.5))->toBeFalse(); // Never completes
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('FadeText Effect Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('fades text in', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);
        $effect = new FadeText(true);

        // Animate fade in
        for ($i = 0; $i <= 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $renderer->applyEffect($effect, 'Fade In', 20, 12, $progress);
            $display->display();
            usleep(100000); // 100ms per step
        }

        expect($effect->isComplete(1.0))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('fades text out', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);
        $effect = new FadeText(false);

        // Animate fade out
        for ($i = 0; $i <= 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $renderer->applyEffect($effect, 'Fade Out', 15, 12, $progress);
            $display->display();
            usleep(100000); // 100ms per step
        }

        expect($effect->isComplete(1.0))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Combined Text Effects', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('demonstrates multiple effects in sequence', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $renderer = new TextRenderer($display);

        // Effect 1: Scroll in
        $scroll = new ScrollingText(ScrollingText::DIRECTION_LEFT, 128);
        for ($i = 0; $i <= 5; $i++) {
            $display->clearDisplay();
            $renderer->applyEffect($scroll, 'Step 1', 0, 4, $i / 5);
            $display->display();
            usleep(50000);
        }

        // Effect 2: Typewriter
        $type = new TypewriterText();
        for ($i = 0; $i <= 5; $i++) {
            $display->clearDisplay();
            $renderer->applyEffect($type, 'Step 2', 0, 16, $i / 5);
            $display->display();
            usleep(100000);
        }

        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

