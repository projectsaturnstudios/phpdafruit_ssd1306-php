<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Concerns\HasEffects;
use PhpdaFruit\SSD1306\Effects\ScrollingText;
use PhpdaFruit\SSD1306\Effects\TypewriterText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

// Test class using HasEffects trait
class TestEffectComponent {
    use HasEffects;

    public $display;

    public function __construct($display) {
        $this->display = $display;
    }
}

describe('HasEffects Trait', function () {
    it('starts with no effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        
        expect($component->getEffects())->toBeEmpty()
            ->and($component->getEffectCount())->toBe(0);
    });

    it('adds effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect = new ScrollingText();
        
        $component->addEffect('scroll', $effect);
        
        expect($component->hasEffect('scroll'))->toBeTrue()
            ->and($component->getEffect('scroll'))->toBe($effect)
            ->and($component->getEffectCount())->toBe(1);
    });

    it('removes effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect = new ScrollingText();
        
        $component->addEffect('scroll', $effect);
        $component->removeEffect('scroll');
        
        expect($component->hasEffect('scroll'))->toBeFalse()
            ->and($component->getEffectCount())->toBe(0);
    });

    it('gets effect by name', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect = new ScrollingText();
        
        $component->addEffect('scroll', $effect);
        
        expect($component->getEffect('scroll'))->toBe($effect);
    });

    it('returns null for non-existent effect', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        
        expect($component->getEffect('missing'))->toBeNull();
    });

    it('clears all effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        
        $component->addEffect('scroll', new ScrollingText());
        $component->addEffect('type', new TypewriterText());
        $component->clearEffects();
        
        expect($component->getEffectCount())->toBe(0);
    });

    it('enables and disables effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        
        expect($component->areEffectsEnabled())->toBeTrue();
        
        $component->disableEffects();
        
        expect($component->areEffectsEnabled())->toBeFalse();
        
        $component->enableEffects();
        
        expect($component->areEffectsEnabled())->toBeTrue();
    });

    it('adds effect with fluent interface', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect = new ScrollingText();
        
        $result = $component->withEffect($effect, 'scroll');
        
        expect($result)->toBe($component)
            ->and($component->hasEffect('scroll'))->toBeTrue();
    });

    it('auto-generates effect names', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect = new ScrollingText();
        
        $component->withEffect($effect);
        
        expect($component->getEffectCount())->toBe(1);
    });

    it('chains method calls', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        
        $result = $component->addEffect('scroll', new ScrollingText())
                            ->addEffect('type', new TypewriterText())
                            ->disableEffects()
                            ->enableEffects();
        
        expect($result)->toBe($component)
            ->and($component->getEffectCount())->toBe(2);
    });

    it('gets all effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect1 = new ScrollingText();
        $effect2 = new TypewriterText();
        
        $component->addEffect('scroll', $effect1);
        $component->addEffect('type', $effect2);
        
        $effects = $component->getEffects();
        
        expect($effects)->toHaveCount(2)
            ->and($effects['scroll'])->toBe($effect1)
            ->and($effects['type'])->toBe($effect2);
    });

    it('resets all effects', function () {
        $display = DisplayFactory::forTesting();
        $component = new TestEffectComponent($display);
        $effect = new ScrollingText();
        
        $component->addEffect('scroll', $effect);
        $component->resetEffects();
        
        // Just verify method completes without error
        expect($component->getEffectCount())->toBe(1);
    });
});

