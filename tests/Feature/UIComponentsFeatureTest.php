<?php
declare(strict_types=1);
use PhpdaFruit\SSD1306\UI\Menu;
use PhpdaFruit\SSD1306\UI\Notification;
use PhpdaFruit\SSD1306\UI\Dashboard;
use PhpdaFruit\SSD1306\UI\Widgets\TextWidget;
use PhpdaFruit\SSD1306\UI\Widgets\ProgressWidget;
use PhpdaFruit\SSD1306\UI\Widgets\IconWidget;
use PhpdaFruit\SSD1306\UI\Widgets\GraphWidget;
use PhpdaFruit\SSD1306\Shapes\Icon;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

beforeAll(function () {
    Icon::initializeBuiltIns();
});

describe('Menu Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('renders menu with navigation', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $menu = new Menu($display, 0, 0, 128, 32);
        
        $menu->addItem('Settings')
             ->addItem('Network')
             ->addItem('Display')
             ->addItem('About');

        // Show menu and navigate
        for ($i = 0; $i < 4; $i++) {
            $display->clearDisplay();
            $menu->render();
            $display->display();
            usleep(500000); // 0.5s
            $menu->selectNext();
        }

        expect($menu->getItemCount())->toBe(4);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Notification Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('shows sliding notification', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $notif = Notification::info($display, 'Hello World!', 2.0);
        
        $notif->show();
        
        // Animate notification
        for ($i = 0; $i < 20; $i++) {
            $display->clearDisplay();
            $notif->update(0.1);
            $notif->render();
            $display->display();
            usleep(100000); // 0.1s
        }

        expect($notif->isActive())->toBeFalse(); // Auto-dismissed
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('shows different priority notifications', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        
        $notifications = [
            Notification::info($display, 'Info', 1.0),
            Notification::warning($display, 'Warning', 1.0),
            Notification::error($display, 'Error', 1.0),
        ];

        foreach ($notifications as $notif) {
            $notif->show();
            
            for ($i = 0; $i < 10; $i++) {
                $display->clearDisplay();
                $notif->update(0.1);
                $notif->render();
                $display->display();
                usleep(100000);
            }
        }

        expect(count($notifications))->toBe(3);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Dashboard Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1500000); // 1.5s pause
        }
    });

    it('renders dashboard with text widgets', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $dashboard = new Dashboard($display, 2, 2);
        
        $w1 = new TextWidget($display);
        $w1->setTitle('CPU')->setText('45%');
        
        $w2 = new TextWidget($display);
        $w2->setTitle('MEM')->setText('78%');
        
        $dashboard->addWidget($w1, 0, 0)
                  ->addWidget($w2, 0, 1);

        // Render dashboard
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $dashboard->render();
            $display->display();
            usleep(200000); // 0.2s
        }

        expect($dashboard->getWidgets())->toHaveCount(2);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders dashboard with progress widgets', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $dashboard = new Dashboard($display, 2, 1);
        
        $w1 = new ProgressWidget($display);
        $w1->setLabel('Load')->setValue(65);
        
        $w2 = new ProgressWidget($display);
        $w2->setLabel('Disk')->setValue(82);
        
        $dashboard->addWidget($w1, 0, 0)
                  ->addWidget($w2, 1, 0);

        // Animate progress
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $dashboard->render();
            $display->display();
            
            $w1->setValue(65 + $i * 3);
            $w2->setValue(82 - $i * 2);
            usleep(200000);
        }

        expect($dashboard->getWidgets())->toHaveCount(2);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders dashboard with icon widgets', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $dashboard = new Dashboard($display, 1, 4);
        
        $icons = ['wifi', 'battery', 'warning', 'info'];
        
        foreach ($icons as $i => $iconName) {
            $widget = new IconWidget($display);
            $widget->setIcon($iconName)->setLabel($iconName);
            $dashboard->addWidget($widget, 0, $i);
        }

        // Render dashboard
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $dashboard->render();
            $display->display();
            usleep(200000);
        }

        expect($dashboard->getWidgets())->toHaveCount(4);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders dashboard with graph widget', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $dashboard = new Dashboard($display, 1, 1);
        
        $graph = new GraphWidget($display);
        $graph->setLabel('CPU')->setMaxPoints(30);
        
        $dashboard->addWidget($graph, 0, 0);

        // Animate graph with data
        for ($i = 0; $i < 30; $i++) {
            $value = 50 + sin($i * 0.2) * 30;
            $graph->addDataPoint($value);
            
            $display->clearDisplay();
            $dashboard->render();
            $display->display();
            usleep(100000);
        }

        expect($dashboard->getWidgets())->toHaveCount(1);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Combined UI Scenario', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause
        }
    });

    it('shows complete UI flow', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        
        // 1. Show notification
        $notif = Notification::info($display, 'System Ready');
        $notif->show();
        
        for ($i = 0; $i < 10; $i++) {
            $display->clearDisplay();
            $notif->update(0.1);
            $notif->render();
            $display->display();
            usleep(100000);
        }
        
        // 2. Show dashboard
        $dashboard = new Dashboard($display, 2, 2);
        
        $cpu = new ProgressWidget($display);
        $cpu->setLabel('CPU')->setValue(45);
        
        $mem = new ProgressWidget($display);
        $mem->setLabel('MEM')->setValue(67);
        
        $icon = new IconWidget($display);
        $icon->setIcon('wifi');
        
        $text = new TextWidget($display);
        $text->setText('OK');
        
        $dashboard->addWidget($cpu, 0, 0)
                  ->addWidget($mem, 0, 1)
                  ->addWidget($icon, 1, 0)
                  ->addWidget($text, 1, 1);

        for ($i = 0; $i < 15; $i++) {
            $display->clearDisplay();
            $dashboard->render();
            $display->display();
            usleep(150000);
        }

        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

