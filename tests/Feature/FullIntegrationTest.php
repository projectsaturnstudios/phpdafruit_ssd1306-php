<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\Builder\DisplayBuilder;
use PhpdaFruit\SSD1306\Services\TextRenderer;
use PhpdaFruit\SSD1306\Services\ShapeRenderer;
use PhpdaFruit\SSD1306\Services\AnimationEngine;
use PhpdaFruit\SSD1306\StateMachine\StateMachine;
use PhpdaFruit\SSD1306\StateMachine\States\IdleState;
use PhpdaFruit\SSD1306\StateMachine\States\AlertState;
use PhpdaFruit\SSD1306\StateMachine\States\DashboardState;
use PhpdaFruit\SSD1306\StateMachine\Transition;
use PhpdaFruit\SSD1306\Effects\ScrollingText;
use PhpdaFruit\SSD1306\Effects\TypewriterText;
use PhpdaFruit\SSD1306\Shapes\ProgressBar;
use PhpdaFruit\SSD1306\Shapes\Icon;
use PhpdaFruit\SSD1306\UI\Menu;
use PhpdaFruit\SSD1306\UI\Notification;
use PhpdaFruit\SSD1306\UI\Dashboard;
use PhpdaFruit\SSD1306\UI\Widgets\TextWidget;
use PhpdaFruit\SSD1306\UI\Widgets\ProgressWidget;
use PhpdaFruit\SSD1306\UI\Widgets\IconWidget;
use PhpdaFruit\SSD1306\Math\Vector2D;
use PhpdaFruit\SSD1306\Math\Curve;

beforeAll(function () {
    Icon::initializeBuiltIns();
});

describe('Full Integration Test Suite', function () {
    
    it('demonstrates complete library workflow', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        // === PHASE 1: Display Builder ===
        $display = DisplayFactory::standard('/dev/i2c-7');
        
        // Show startup notification
        $notification = Notification::info($display, 'System Init', 1.5);
        $notification->show();
        
        for ($i = 0; $i < 15; $i++) {
            $display->clearDisplay();
            $notification->update(0.1);
            $notification->render();
            $display->display();
            usleep(100000);
        }

        // === PHASE 2: Text Rendering with Effects ===
        $textRenderer = new TextRenderer($display);
        $typewriter = new TypewriterText();
        
        for ($i = 0; $i < 10; $i++) {
            $progress = $i / 10;
            $display->clearDisplay();
            $typewriter->render($display, 'Loading...', 25, 12, $progress);
            $display->display();
            usleep(150000);
        }
        
        usleep(500000);

        // === PHASE 3: Shape Rendering ===
        $shapeRenderer = new ShapeRenderer($display);
        
        for ($i = 0; $i <= 10; $i++) {
            $percent = $i * 10;
            $progressBar = new ProgressBar(10, 20, 108, 8, $percent);
            
            $display->clearDisplay();
            $display->setCursor(35, 10);
            $display->setTextSize(1);
            $display->setTextColor(1);
            foreach (str_split('Progress') as $char) {
                $display->write(ord($char));
            }
            $shapeRenderer->progressBar($progressBar);
            $display->display();
            usleep(150000);
        }
        
        usleep(500000);

        // === PHASE 4: Animation Engine ===
        $animation = new AnimationEngine($display);
        
        // Create bouncing ball animation
        for ($x = 10; $x <= 100; $x += 15) {
            $animation->addFrame(function($disp, $progress) use ($x) {
                $disp->drawCircle($x, 16, 6, 1);
                $disp->setCursor(95, 24);
                $disp->setTextSize(1);
                $disp->setTextColor(1);
                foreach (str_split('Go!') as $char) {
                    $disp->write(ord($char));
                }
            }, 100);
        }
        
        $animation->play();
        usleep(1000000);

        // === PHASE 5: Dashboard UI ===
        $dashboard = new Dashboard($display, 2, 2);
        
        $cpuWidget = new ProgressWidget($display);
        $cpuWidget->setLabel('CPU')->setValue(65);
        
        $memWidget = new ProgressWidget($display);
        $memWidget->setLabel('MEM')->setValue(78);
        
        $statusWidget = new TextWidget($display);
        $statusWidget->setText('OK')->setShowBorder(false);
        
        $iconWidget = new IconWidget($display);
        $iconWidget->setIcon('wifi');
        
        $dashboard->addWidget($cpuWidget, 0, 0)
                  ->addWidget($memWidget, 0, 1)
                  ->addWidget($statusWidget, 1, 0)
                  ->addWidget($iconWidget, 1, 1);

        // Render dashboard with live updates
        for ($i = 0; $i < 10; $i++) {
            $cpuWidget->setValue(65 + sin($i * 0.5) * 10);
            $memWidget->setValue(78 + cos($i * 0.5) * 5);
            
            $display->clearDisplay();
            $dashboard->render();
            $display->display();
            usleep(200000);
        }

        usleep(500000);

        // === PHASE 6: Menu Navigation ===
        $menu = new Menu($display, 0, 0, 128, 32);
        $menu->addItem('Settings')
             ->addItem('Display')
             ->addItem('Network')
             ->addItem('About');

        for ($i = 0; $i < 4; $i++) {
            $display->clearDisplay();
            $menu->render();
            $display->display();
            usleep(400000);
            $menu->selectNext();
        }

        usleep(500000);

        // === PHASE 7: State Machine ===
        $stateMachine = new StateMachine($display);
        $stateMachine->addState('idle', new IdleState($display));
        $stateMachine->addState('alert', new AlertState($display, 'Warning!'));
        $stateMachine->addState('dashboard', new DashboardState($display));
        
        // Start with idle state
        $stateMachine->transition('idle');

        // Idle state
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $stateMachine->update();
            $stateMachine->render();
            $display->display();
            usleep(100000);
        }

        // Transition to alert
        $transition = new Transition('fade', 0.5);
        $stateMachine->transition('alert', $transition);
        
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $stateMachine->update();
            $stateMachine->render();
            $display->display();
            usleep(100000);
        }

        // Transition to dashboard
        $stateMachine->transition('dashboard');
        
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $stateMachine->update();
            $stateMachine->render();
            $display->display();
            usleep(100000);
        }

        usleep(500000);

        // === PHASE 8: Math Utilities Demo ===
        $display->clearDisplay();
        
        // Draw rotating vector
        for ($angle = 0; $angle < M_PI * 2; $angle += M_PI / 8) {
            $vector = Vector2D::fromAngle($angle, 15);
            $centerX = 64;
            $centerY = 16;
            
            $endX = (int)($centerX + $vector->x);
            $endY = (int)($centerY + $vector->y);
            
            $display->clearDisplay();
            $display->drawLine($centerX, $centerY, $endX, $endY, 1);
            $display->drawCircle($centerX, $centerY, 3, 1);
            $display->display();
            usleep(100000);
        }

        usleep(500000);

        // === FINAL: Success Message ===
        $successNotif = Notification::info($display, 'All Systems Go!', 2.0);
        $successNotif->show();
        
        for ($i = 0; $i < 20; $i++) {
            $display->clearDisplay();
            $successNotif->update(0.1);
            $successNotif->render();
            $display->display();
            usleep(100000);
        }

        // Final clear
        $display->clearDisplay();
        $display->display();

        expect(true)->toBeTrue(); // If we made it here, everything works!
        
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('tests builder pattern with all options', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = (new DisplayBuilder())
            ->size(128, 32)
            ->on('/dev/i2c-7')
            ->i2cAddress(0x3C)
            ->vccState(1)
            ->rotation(0)
            ->inverted(false)
            ->brightness(128)
            ->textSize(1)
            ->textColor(1)
            ->textWrap(true)
            ->build();

        $display->clearDisplay();
        $display->setCursor(20, 12);
        foreach (str_split('Builder!') as $char) {
            $display->write(ord($char));
        }
        $display->display();
        
        usleep(1000000);

        expect($display)->toBeInstanceOf(\PhpdaFruit\SSD1306\SSD1306Display::class);
        
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('tests all factory presets', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $presets = [
            'Standard' => DisplayFactory::standard('/dev/i2c-7'),
            'Dimmed' => DisplayFactory::dimmed('/dev/i2c-7'),
            'HighContrast' => DisplayFactory::highContrast('/dev/i2c-7'),
        ];

        foreach ($presets as $name => $display) {
            $display->clearDisplay();
            $display->setCursor(30, 12);
            $display->setTextSize(1);
            $display->setTextColor(1);
            foreach (str_split($name) as $char) {
                $display->write(ord($char));
            }
            $display->display();
            usleep(800000);
        }

        expect(count($presets))->toBe(3);
        
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

