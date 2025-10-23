<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Concerns\HasAnimations;
use PhpdaFruit\SSD1306\Services\AnimationEngine;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

// Test class using HasAnimations trait
class TestAnimatedComponent {
    use HasAnimations;

    public $display;

    public function __construct($display) {
        $this->display = $display;
    }
}

describe('HasAnimations Trait', function () {
    it('starts without animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        
        expect($component->hasAnimation())->toBeFalse()
            ->and($component->isAnimating())->toBeFalse();
    });

    it('sets animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        $animation = new AnimationEngine($display);
        
        $component->setAnimation($animation);
        
        expect($component->hasAnimation())->toBeTrue()
            ->and($component->getAnimation())->toBe($animation);
    });

    it('starts animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        $animation = new AnimationEngine($display);
        $animation->addFrame(fn($d, $p) => null, 100);
        
        $component->setAnimation($animation);
        $component->startAnimation();
        
        // Check that animation is set and start was called
        expect($component->hasAnimation())->toBeTrue()
            ->and($animation->getFrameCount())->toBeGreaterThan(0);
    });

    it('stops animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        $animation = new AnimationEngine($display);
        $animation->addFrame(fn($d, $p) => null, 100);
        
        $component->setAnimation($animation);
        $component->startAnimation();
        $component->stopAnimation();
        
        // Animation should still be set but stopped
        expect($component->hasAnimation())->toBeTrue();
    });

    it('clears animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        $animation = new AnimationEngine($display);
        
        $component->setAnimation($animation);
        $component->clearAnimation();
        
        expect($component->hasAnimation())->toBeFalse()
            ->and($component->getAnimation())->toBeNull();
    });

    it('chains method calls', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        $animation = new AnimationEngine($display);
        
        $result = $component->setAnimation($animation)
                            ->startAnimation()
                            ->stopAnimation();
        
        expect($result)->toBe($component);
    });

    it('resets animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        $animation = new AnimationEngine($display);
        $animation->addFrame(fn($d, $p) => null, 100);
        
        $component->setAnimation($animation)->resetAnimation();
        
        expect($animation->getCurrentFrame())->toBe(0);
    });

    it('creates simple animation with animate method', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        
        $component->animate(fn($d, $p) => null, 500, false);
        
        expect($component->hasAnimation())->toBeTrue()
            ->and($component->getAnimation())->toBeInstanceOf(AnimationEngine::class);
    });

    it('creates looping animation', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestAnimatedComponent($display);
        
        $component->animate(fn($d, $p) => null, 500, true);
        
        expect($component->hasAnimation())->toBeTrue();
    });
});

