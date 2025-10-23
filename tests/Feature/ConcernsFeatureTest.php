<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Concerns\HasAnimations;
use PhpdaFruit\SSD1306\Concerns\HasEffects;
use PhpdaFruit\SSD1306\Concerns\Renderable;
use PhpdaFruit\SSD1306\Services\AnimationEngine;
use PhpdaFruit\SSD1306\Effects\ScrollingText;
use PhpdaFruit\SSD1306\Effects\TypewriterText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\SSD1306Display;

// Feature test component using all traits
class FeatureTestWidget implements Renderable {
    use HasAnimations;
    use HasEffects;

    private bool $visible = true;
    private string $text = '';

    public function __construct(
        private SSD1306Display $display,
        private int $x = 0,
        private int $y = 0,
        private int $width = 128,
        private int $height = 32
    ) {}

    public function setText(string $text): self {
        $this->text = $text;
        return $this;
    }

    public function render(): void {
        if (!$this->visible || !$this->text) {
            return;
        }

        $this->display->setCursor($this->x, $this->y);
        $this->display->setTextSize(1);
        $this->display->setTextColor(1);
        
        foreach (str_split($this->text) as $char) {
            $this->display->write(ord($char));
        }
    }

    public function getBounds(): array {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height
        ];
    }

    public function setVisible(bool $visible): self {
        $this->visible = $visible;
        return $this;
    }

    public function isVisible(): bool {
        return $this->visible;
    }
}

describe('Concerns Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('uses Renderable interface for visibility control', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $widget = new FeatureTestWidget($display, 20, 12);
        $widget->setText('Visible');

        // Show widget
        for ($i = 0; $i < 5; $i++) {
            $display->clearDisplay();
            $widget->render();
            $display->display();
            usleep(200000);
        }

        // Hide widget
        $widget->setVisible(false);
        for ($i = 0; $i < 5; $i++) {
            $display->clearDisplay();
            $widget->render();
            $display->display();
            usleep(200000);
        }

        expect($widget->isVisible())->toBeFalse();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('uses HasAnimations trait for animated widget', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $widget = new FeatureTestWidget($display, 0, 12);
        
        // Create simple animation
        $animation = new AnimationEngine($display);
        
        for ($x = 0; $x <= 80; $x += 20) {
            $animation->addFrame(function($disp, $progress) use ($x) {
                $disp->fillCircle($x + 10, 16, 4, 1);
            }, 150);
        }
        
        $widget->setAnimation($animation);
        $widget->startAnimation();

        // Verify animation is attached and has frames
        expect($widget->hasAnimation())->toBeTrue()
            ->and($widget->getAnimation()->getFrameCount())->toBeGreaterThan(0);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('uses HasEffects trait for text effects', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $widget = new FeatureTestWidget($display, 10, 12);
        $widget->setText('Effects!');
        
        // Add scrolling effect
        $widget->addEffect('scroll', new ScrollingText());

        // Render with effect
        for ($i = 0; $i < 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $widget->applyEffects('Effects!', 10, 12, $progress);
            $display->display();
            usleep(150000);
        }

        expect($widget->hasEffect('scroll'))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('combines all traits for complex widget', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $widget = new FeatureTestWidget($display, 15, 12);
        $widget->setText('Complete!');
        
        // Add effect
        $widget->addEffect('type', new TypewriterText());
        
        // Render with effect
        for ($i = 0; $i < 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $widget->applyEffects('Complete!', 15, 12, $progress);
            $display->display();
            usleep(200000);
        }
        
        // Toggle visibility
        $widget->setVisible(false);
        $display->clearDisplay();
        $widget->render();
        $display->display();
        usleep(500000);
        
        $widget->setVisible(true);
        $display->clearDisplay();
        $widget->render();
        $display->display();

        expect($widget)->toBeInstanceOf(Renderable::class)
            ->and($widget->hasEffect('type'))->toBeTrue()
            ->and($widget->isVisible())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('demonstrates effect enable/disable', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $widget = new FeatureTestWidget($display, 20, 12);
        $widget->setText('Toggle');
        
        $widget->addEffect('scroll', new ScrollingText());
        
        // With effects enabled
        $widget->enableEffects();
        for ($i = 0; $i < 5; $i++) {
            $display->clearDisplay();
            $widget->applyEffects('Toggle', 20, 12, $i / 5);
            $display->display();
            usleep(150000);
        }
        
        // With effects disabled
        $widget->disableEffects();
        for ($i = 0; $i < 5; $i++) {
            $display->clearDisplay();
            $widget->render(); // Render without effects
            $display->display();
            usleep(150000);
        }

        expect($widget->areEffectsEnabled())->toBeFalse();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('demonstrates fluent interface with traits', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $widget = new FeatureTestWidget($display, 25, 12);
        
        // Chain all trait methods
        $widget->setText('Fluent')
               ->withEffect(new TypewriterText())
               ->setVisible(true);
        
        // Render
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $widget->applyEffects('Fluent', 25, 12, $i / 10);
            $display->display();
            usleep(150000);
        }

        expect($widget->getEffectCount())->toBeGreaterThan(0)
            ->and($widget->isVisible())->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

