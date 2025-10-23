# SSD1306 PHP Package - Examples

Complete usage examples demonstrating the full capabilities of the SSD1306 PHP library.

## Table of Contents

1. [System Monitor Dashboard](#system-monitor-dashboard)
2. [Interactive Menu System](#interactive-menu-system)
3. [Animated Splash Screen](#animated-splash-screen)
4. [Notification System](#notification-system)
5. [State Machine Workflow](#state-machine-workflow)
6. [Custom Widget](#custom-widget)
7. [Game Demo](#game-demo)

---

## System Monitor Dashboard

Display real-time system metrics with progress bars and gauges.

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\UI\Dashboard;
use PhpdaFruit\SSD1306\UI\Widgets\{ProgressWidget, TextWidget, IconWidget};
use PhpdaFruit\SSD1306\Shapes\Icon;

require 'vendor/autoload.php';

// Initialize
Icon::initializeBuiltIns();
$display = DisplayFactory::standard('/dev/i2c-7');

// Create dashboard with 2x2 grid
$dashboard = new Dashboard($display, 2, 2);

// CPU widget
$cpuWidget = new ProgressWidget($display);
$cpuWidget->setLabel('CPU')->setShowPercent(true);

// Memory widget
$memWidget = new ProgressWidget($display);
$memWidget->setLabel('MEM')->setShowPercent(true);

// Status text
$statusWidget = new TextWidget($display);
$statusWidget->setText('System OK')->setShowBorder(false);

// Network icon
$networkWidget = new IconWidget($display);
$networkWidget->setIcon('wifi')->setLabel('Net');

// Add widgets to grid
$dashboard->addWidget($cpuWidget, 0, 0)
          ->addWidget($memWidget, 0, 1)
          ->addWidget($statusWidget, 1, 0)
          ->addWidget($networkWidget, 1, 1);

// Update loop
while (true) {
    // Get actual system metrics (pseudo-code)
    $cpuUsage = sys_getloadavg()[0] * 100;
    $memUsage = memory_get_usage() / memory_get_peak_usage() * 100;
    
    // Update widgets
    $cpuWidget->setValue($cpuUsage);
    $memWidget->setValue($memUsage);
    
    // Render
    $display->clearDisplay();
    $dashboard->render();
    $display->display();
    
    usleep(500000); // Update every 500ms
}
```

---

## Interactive Menu System

Create navigable menus with callbacks.

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\UI\Menu;
use PhpdaFruit\SSD1306\UI\Notification;

require 'vendor/autoload.php';

$display = DisplayFactory::standard('/dev/i2c-7');

// Create main menu
$menu = new Menu($display);
$menu->addItem('Settings', function() use ($display) {
    $notif = Notification::info($display, 'Settings opened');
    $notif->show();
    // ... open settings menu
})
->addItem('Display', function() use ($display) {
    // ... adjust display settings
})
->addItem('Network', function() use ($display) {
    // ... network configuration
})
->addItem('About', function() use ($display) {
    // ... show about info
});

// Navigation loop
$running = true;
while ($running) {
    $display->clearDisplay();
    $menu->render();
    $display->display();
    
    // Read input (pseudo-code)
    $input = readInput();
    
    switch ($input) {
        case 'UP':
            $menu->selectPrevious();
            break;
        case 'DOWN':
            $menu->selectNext();
            break;
        case 'SELECT':
            $menu->activate();
            break;
        case 'EXIT':
            $running = false;
            break;
    }
    
    usleep(100000);
}
```

---

## Animated Splash Screen

Create an engaging startup animation.

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\Services\{AnimationEngine, TextRenderer};
use PhpdaFruit\SSD1306\Effects\TypewriterText;
use PhpdaFruit\SSD1306\Shapes\Icon;

require 'vendor/autoload.php';

Icon::initializeBuiltIns();
$display = DisplayFactory::standard('/dev/i2c-7');

// Phase 1: Logo animation
$animation = new AnimationEngine($display);

for ($size = 0; $size <= 20; $size += 4) {
    $animation->addFrame(function($disp, $progress) use ($size) {
        $disp->fillCircle(64, 16, $size, 1);
    }, 80);
}

$animation->play();
usleep(800000);

// Phase 2: Typewriter company name
$textRenderer = new TextRenderer($display);
$typewriter = new TypewriterText();

for ($i = 0; $i < 10; $i++) {
    $display->clearDisplay();
    $typewriter->render($display, 'MyCompany', 25, 8, $i / 10);
    $display->display();
    usleep(100000);
}

// Phase 3: Fade in tagline
sleep(1);
$display->clearDisplay();
$display->setCursor(15, 20);
$display->setTextSize(1);
$display->setTextColor(1);
foreach (str_split('Powered by SSD1306') as $char) {
    $display->write(ord($char));
}
$display->display();

sleep(2);

// Phase 4: Ready notification
$notif = Notification::info($display, 'System Ready', 2.0);
$notif->show();

for ($i = 0; $i < 20; $i++) {
    $display->clearDisplay();
    $notif->update(0.1);
    $notif->render();
    $display->display();
    usleep(100000);
}
```

---

## Notification System

Queue and display notifications with priorities.

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\UI\Notification;

require 'vendor/autoload.php';

$display = DisplayFactory::standard('/dev/i2c-7');

class NotificationQueue {
    private array $queue = [];
    private ?Notification $current = null;
    
    public function add(Notification $notification): void {
        $this->queue[] = $notification;
    }
    
    public function update(float $deltaTime): void {
        if ($this->current === null || !$this->current->isActive()) {
            if (!empty($this->queue)) {
                $this->current = array_shift($this->queue);
                $this->current->show();
            }
        }
        
        if ($this->current) {
            $this->current->update($deltaTime);
        }
    }
    
    public function render(): void {
        if ($this->current) {
            $this->current->render();
        }
    }
}

// Usage
$notifQueue = new NotificationQueue();

// Add notifications
$notifQueue->add(Notification::info($display, 'Welcome!'));
$notifQueue->add(Notification::warning($display, 'Low battery'));
$notifQueue->add(Notification::error($display, 'Connection lost'));

// Render loop
while (true) {
    $display->clearDisplay();
    
    // Your main content here
    $display->setCursor(20, 20);
    foreach (str_split('Main Screen') as $char) {
        $display->write(ord($char));
    }
    
    // Overlay notifications
    $notifQueue->update(0.1);
    $notifQueue->render();
    
    $display->display();
    usleep(100000);
}
```

---

## State Machine Workflow

Manage complex display workflows with state machines.

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\StateMachine\{StateMachine, DisplayState, Transition};
use PhpdaFruit\SSD1306\Math\Curve;

require 'vendor/autoload.php';

// Custom states
class SplashState extends DisplayState {
    private float $elapsed = 0;
    
    public function update(float $deltaTime): void {
        $this->elapsed += $deltaTime;
        if ($this->elapsed >= 3.0) {
            // Auto-transition to menu
            $this->getStateMachine()->transition('menu');
        }
    }
    
    public function render(): void {
        $this->display->setCursor(30, 12);
        $this->display->setTextSize(2);
        $this->display->setTextColor(1);
        foreach (str_split('LOGO') as $char) {
            $this->display->write(ord($char));
        }
    }
}

class MenuState extends DisplayState {
    public function render(): void {
        $this->display->setCursor(35, 8);
        $this->display->setTextSize(1);
        $this->display->setTextColor(1);
        foreach (str_split('Main Menu') as $char) {
            $this->display->write(ord($char));
        }
        $this->display->drawRect(10, 5, 108, 22, 1);
    }
}

class WorkingState extends DisplayState {
    private float $progress = 0;
    
    public function update(float $deltaTime): void {
        $this->progress += $deltaTime * 0.2;
        if ($this->progress >= 1.0) {
            $this->getStateMachine()->transition('complete');
        }
    }
    
    public function render(): void {
        $this->display->setCursor(30, 8);
        foreach (str_split('Working...') as $char) {
            $this->display->write(ord($char));
        }
        
        $barWidth = (int)($this->progress * 100);
        $this->display->fillRect(14, 18, $barWidth, 8, 1);
        $this->display->drawRect(13, 17, 102, 10, 1);
    }
}

// Setup state machine
$display = DisplayFactory::standard('/dev/i2c-7');
$stateMachine = new StateMachine($display);

$stateMachine->addState('splash', new SplashState($display));
$stateMachine->addState('menu', new MenuState($display));
$stateMachine->addState('working', new WorkingState($display));

$stateMachine->setInitialState('splash');

// Main loop
while (true) {
    $display->clearDisplay();
    $stateMachine->update(0.1);
    $stateMachine->render();
    $display->display();
    
    usleep(100000);
}
```

---

## Custom Widget

Create reusable widgets with effects and animations.

```php
<?php

use PhpdaFruit\SSD1306\UI\Widget;
use PhpdaFruit\SSD1306\Concerns\{HasAnimations, HasEffects};

class ClockWidget extends Widget {
    use HasAnimations;
    use HasEffects;
    
    private string $time = '00:00';
    private bool $colonBlink = false;
    
    public function setTime(string $time): self {
        $this->time = $time;
        return $this;
    }
    
    public function update(float $deltaTime): void {
        // Blink colon every second
        static $elapsed = 0;
        $elapsed += $deltaTime;
        
        if ($elapsed >= 1.0) {
            $this->colonBlink = !$this->colonBlink;
            $elapsed = 0;
        }
    }
    
    public function render(): void {
        if (!$this->visible) {
            return;
        }
        
        // Display time
        $displayTime = $this->colonBlink 
            ? str_replace(':', ' ', $this->time)
            : $this->time;
        
        $this->display->setCursor($this->x, $this->y);
        $this->display->setTextSize(2);
        $this->display->setTextColor(1);
        
        foreach (str_split($displayTime) as $char) {
            $this->display->write(ord($char));
        }
    }
}

// Usage
$display = DisplayFactory::standard('/dev/i2c-7');
$clock = new ClockWidget($display, 30, 12, 68, 16);

while (true) {
    $clock->setTime(date('H:i'));
    
    $display->clearDisplay();
    $clock->update(0.1);
    $clock->render();
    $display->display();
    
    usleep(100000);
}
```

---

## Game Demo

Simple game using animation and math utilities.

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\Math\Vector2D;

require 'vendor/autoload.php';

$display = DisplayFactory::standard('/dev/i2c-7');

// Ball physics
$position = new Vector2D(64, 16);
$velocity = new Vector2D(2, 1);
$radius = 3;

// Game loop
while (true) {
    // Update position
    $position = $position->add($velocity);
    
    // Bounce off walls
    if ($position->x <= $radius || $position->x >= 128 - $radius) {
        $velocity = new Vector2D(-$velocity->x, $velocity->y);
    }
    if ($position->y <= $radius || $position->y >= 32 - $radius) {
        $velocity = new Vector2D($velocity->x, -$velocity->y);
    }
    
    // Render
    $display->clearDisplay();
    $display->fillCircle((int)$position->x, (int)$position->y, $radius, 1);
    $display->display();
    
    usleep(50000);
}
```

---

## Tips & Tricks

### Smooth Animations

Use easing functions for natural motion:

```php
use PhpdaFruit\SSD1306\Math\Curve;

for ($i = 0; $i <= 100; $i++) {
    $progress = $i / 100;
    $eased = Curve::easeInOut($progress);
    $x = 10 + ($eased * 100);
    
    $display->clearDisplay();
    $display->fillCircle((int)$x, 16, 5, 1);
    $display->display();
    usleep(20000);
}
```

### Memory-Efficient Rendering

Clear only what changed:

```php
// Instead of full clear
$display->clearDisplay();

// Clear specific region
$display->fillRect($x, $y, $width, $height, 0);
```

### Responsive Layouts

Use widget bounds for dynamic sizing:

```php
$widget->setBounds(
    $x,
    $y,
    $display->getDisplayWidth() / 2,
    $display->getDisplayHeight() / 2
);
```

---

For more examples and documentation, see [README.md](README.md).

