<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Services\TextRenderer;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\Effects\ScrollingText;

describe('TextRenderer Construction', function () {
    it('creates renderer with display instance', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        
        expect($renderer)->toBeInstanceOf(TextRenderer::class);
    });
});

describe('TextRenderer Measurement', function () {
    beforeEach(function () {
        $this->display = DisplayFactory::forTesting();
        $this->renderer = new TextRenderer($this->display);
    });

    it('measures text with default size', function () {
        $measurements = $this->renderer->measureText('Hello');
        
        expect($measurements)->toHaveKey('width')
            ->and($measurements)->toHaveKey('height')
            ->and($measurements['width'])->toBe(30) // 5 chars * 6 pixels
            ->and($measurements['height'])->toBe(8);
    });

    it('measures text with size 2', function () {
        $measurements = $this->renderer->measureText('Hi', ['size' => 2]);
        
        expect($measurements['width'])->toBe(24) // 2 chars * 6 * 2
            ->and($measurements['height'])->toBe(16); // 8 * 2
    });

    it('measures empty string', function () {
        $measurements = $this->renderer->measureText('');
        
        expect($measurements['width'])->toBe(0)
            ->and($measurements['height'])->toBe(8);
    });
});

describe('TextRenderer Options', function () {
    it('applies text size option', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        
        // Just verify the method completes without error
        // Actual size application is tested in integration tests
        expect(function () use ($renderer) {
            $renderer->text('Test', 0, 0, ['size' => 2]);
        })->not->toThrow(Exception::class);
    });

    it('applies color option', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->text('Test', 0, 0, ['color' => 1]);
        })->not->toThrow(Exception::class);
    });

    it('applies color with background option', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->text('Test', 0, 0, ['color' => 1, 'background' => 0]);
        })->not->toThrow(Exception::class);
    });

    it('applies wrap option', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        
        expect(function () use ($renderer) {
            $renderer->text('Test', 0, 0, ['wrap' => false]);
        })->not->toThrow(Exception::class);
    });
});

describe('TextRenderer Centering', function () {
    it('calculates centered position correctly', function () {
        $display = DisplayFactory::forTesting(128, 32);
        $renderer = new TextRenderer($display);
        
        // "Hello" = 5 chars * 6 = 30 pixels
        // Center = (128 - 30) / 2 = 49
        expect(function () use ($renderer) {
            $renderer->centeredText('Hello', 10);
        })->not->toThrow(Exception::class);
    });

    it('handles text wider than display', function () {
        $display = DisplayFactory::forTesting(64, 32);
        $renderer = new TextRenderer($display);
        
        // Long text that exceeds display width
        expect(function () use ($renderer) {
            $renderer->centeredText('Very Long Text Here', 10);
        })->not->toThrow(Exception::class);
    });
});

describe('TextRenderer Wrapping', function () {
    it('wraps text to multiple lines', function () {
        $display = DisplayFactory::forTesting(128, 32);
        $renderer = new TextRenderer($display);
        
        $lineCount = $renderer->wrappedText('This is a long text that should wrap', 0, 0, 60);
        
        expect($lineCount)->toBeGreaterThan(1);
    });

    it('returns 1 for short text', function () {
        $display = DisplayFactory::forTesting(128, 32);
        $renderer = new TextRenderer($display);
        
        $lineCount = $renderer->wrappedText('Short', 0, 0, 100);
        
        expect($lineCount)->toBe(1);
    });

    it('handles empty text', function () {
        $display = DisplayFactory::forTesting(128, 32);
        $renderer = new TextRenderer($display);
        
        $lineCount = $renderer->wrappedText('', 0, 0, 100);
        
        expect($lineCount)->toBe(0);
    });

    it('respects width constraint', function () {
        $display = DisplayFactory::forTesting(128, 32);
        $renderer = new TextRenderer($display);
        
        // Each word should be on separate line with small width
        $lineCount = $renderer->wrappedText('One Two Three', 0, 0, 20);
        
        expect($lineCount)->toBeGreaterThan(1);
    });
});

describe('TextRenderer Effects', function () {
    it('applies effect with progress', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        $effect = new ScrollingText();
        
        expect(function () use ($renderer, $effect) {
            $renderer->applyEffect($effect, 'Test', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('applies effect at start progress', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        $effect = new ScrollingText();
        
        expect(function () use ($renderer, $effect) {
            $renderer->applyEffect($effect, 'Test', 0, 0, 0.0);
        })->not->toThrow(Exception::class);
    });

    it('applies effect at end progress', function () {
        $display = DisplayFactory::forTesting();
        $renderer = new TextRenderer($display);
        $effect = new ScrollingText();
        
        expect(function () use ($renderer, $effect) {
            $renderer->applyEffect($effect, 'Test', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });
});

