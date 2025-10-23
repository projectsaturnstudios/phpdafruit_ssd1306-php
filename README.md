# SSD1306 PHP Package

A comprehensive, production-ready PHP library for controlling SSD1306 OLED displays via I2C. Built on top of the `php-ssd1306-i2c` extension with high-level services, animations, state machines, and UI components.

## Features

🎨 **Rich Feature Set:**
- **Builder Pattern**: Fluent display configuration with presets
- **Text Rendering**: Advanced text rendering with effects (scroll, typewriter, marquee, fade)
- **Shape Rendering**: Progress bars, gauges, rounded boxes, icons
- **Animation Engine**: Frame-based animations with timing control
- **State Machine**: Manage display states with smooth transitions
- **UI Components**: Menus, notifications, dashboards, widgets
- **Math Utilities**: 2D vectors, matrices, curves, and easing functions
- **Traits & Interfaces**: Composable functionality with `HasAnimations`, `HasEffects`, `Renderable`

🧪 **Production Ready:**
- 450+ comprehensive Pest tests
- All tests run on actual hardware
- Zero segfaults with proper resource management
- Full PSR-4 autoloading

## Requirements

- PHP 8.1+
- `php-ssd1306-i2c` extension installed
- `php-adafruit-gfx` extension installed
- I2C device access (e.g., `/dev/i2c-7`)

## Installation

```bash
composer require phpdafruit/ssd1306-php
```

## Quick Start

```php
<?php

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\Services\TextRenderer;
use PhpdaFruit\SSD1306\Effects\TypewriterText;

// Create display using factory
$display = DisplayFactory::standard('/dev/i2c-7');

// Render text with typewriter effect
$textRenderer = new TextRenderer($display);
$effect = new TypewriterText();

for ($i = 0; $i < 10; $i++) {
    $display->clearDisplay();
    $effect->render($display, 'Hello World!', 10, 12, $i / 10);
    $display->display();
    usleep(100000);
}
```

## Core Components

### 1. Display Builder

Create and configure displays with a fluent API:

```php
use PhpdaFruit\SSD1306\Builder\DisplayBuilder;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

// Manual configuration
$display = (new DisplayBuilder())
    ->withDisplay(128, 32, '/dev/i2c-7')
    ->withBrightness(200)
    ->withRotation(0)
    ->build();

// Or use presets
$display = DisplayFactory::standard('/dev/i2c-7');
$display = DisplayFactory::highContrast('/dev/i2c-7');
$display = DisplayFactory::dimmed('/dev/i2c-7');
```

### 2. Text Rendering & Effects

Render text with various effects:

```php
use PhpdaFruit\SSD1306\Services\TextRenderer;
use PhpdaFruit\SSD1306\Effects\{ScrollingText, TypewriterText, MarqueeText, FadeText};

$renderer = new TextRenderer($display);

// Scrolling text
$scroll = new ScrollingText('horizontal', 2);
$scroll->render($display, 'Scrolling...', 0, 10, 0.5);

// Typewriter effect
$typewriter = new TypewriterText();
$typewriter->render($display, 'Typing...', 10, 10, 0.75);

// Positioned text
$renderer->leftAlign('Left', 0, 0);
$renderer->centerAlign('Center', 16);
$renderer->rightAlign('Right', 24);
```

### 3. Shape Rendering

Draw progress bars, gauges, and more:

```php
use PhpdaFruit\SSD1306\Services\ShapeRenderer;
use PhpdaFruit\SSD1306\Shapes\{ProgressBar, Gauge, RoundedBox, Icon};

$shapeRenderer = new ShapeRenderer($display);

// Progress bar
$progressBar = new ProgressBar(10, 10, 100, 8);
$progressBar->setValue(75);
$shapeRenderer->renderProgressBar($progressBar);

// Gauge
$gauge = new Gauge(64, 16, 15);
$gauge->setValue(65)->setRange(0, 100);
$shapeRenderer->renderGauge($gauge);

// Rounded box
$box = new RoundedBox(5, 5, 118, 22, 3);
$shapeRenderer->renderRoundedBox($box);

// Icons
Icon::initializeBuiltIns();
$icon = Icon::get('wifi');
$shapeRenderer->renderIcon($icon, 50, 12);
```

### 4. Animation Engine

Create smooth frame-based animations:

```php
use PhpdaFruit\SSD1306\Services\AnimationEngine;

$animation = new AnimationEngine($display);

// Add frames
for ($x = 0; $x < 100; $x += 10) {
    $animation->addFrame(function($disp, $progress) use ($x) {
        $disp->fillCircle($x, 16, 5, 1);
    }, 100); // 100ms per frame
}

// Control playback
$animation->loop(true);
$animation->play();
```

### 5. State Machine

Manage display states with transitions:

```php
use PhpdaFruit\SSD1306\StateMachine\{StateMachine, Transition};
use PhpdaFruit\SSD1306\StateMachine\States\{IdleState, AlertState, DashboardState};

$stateMachine = new StateMachine($display);

// Register states
$stateMachine->addState('idle', new IdleState($display));
$stateMachine->addState('alert', new AlertState($display, 'Warning!'));
$stateMachine->addState('dashboard', new DashboardState($display));

// Set initial state
$stateMachine->setInitialState('idle');

// Transition with fade effect
$transition = new Transition(0.5, Curve::easeInOut(...), 'fade');
$stateMachine->transition('alert', $transition);

// Update and render
$stateMachine->update(0.016); // Delta time
$stateMachine->render();
```

### 6. UI Components

Build interactive interfaces:

```php
use PhpdaFruit\SSD1306\UI\{Menu, Notification, Dashboard};
use PhpdaFruit\SSD1306\UI\Widgets\{TextWidget, ProgressWidget, IconWidget, GraphWidget};

// Menu
$menu = new Menu($display);
$menu->addItem('Settings', fn() => loadSettings())
     ->addItem('Display', fn() => displayMenu())
     ->addItem('About', fn() => showAbout());
     
$menu->selectNext();
$menu->render();

// Notification
$notification = Notification::info($display, 'System Ready', 3.0);
$notification->show();

// Dashboard with widgets
$dashboard = new Dashboard($display, 2, 2);

$cpu = new ProgressWidget($display);
$cpu->setLabel('CPU')->setValue(65);

$mem = new ProgressWidget($display);
$mem->setLabel('MEM')->setValue(78);

$dashboard->addWidget($cpu, 0, 0)
          ->addWidget($mem, 0, 1);
          
$dashboard->render();
```

### 7. Math Utilities

Vector and matrix operations:

```php
use PhpdaFruit\SSD1306\Math\{Vector2D, Matrix2D, Curve};

// Vector operations
$v1 = new Vector2D(10, 20);
$v2 = new Vector2D(5, 10);
$sum = $v1->add($v2);
$magnitude = $v1->magnitude();

// Matrix transformations
$matrix = Matrix2D::rotation(M_PI / 4);
$transformed = $matrix->transform($v1);

// Easing functions
$eased = Curve::easeInOut(0.5);
$bezier = Curve::cubicBezier($p0, $p1, $p2, $p3, 0.5);
```

### 8. Traits & Interfaces

Add composable functionality:

```php
use PhpdaFruit\SSD1306\Concerns\{HasAnimations, HasEffects, Renderable};

class MyWidget implements Renderable {
    use HasAnimations;
    use HasEffects;
    
    public function render(): void {
        // Render logic
    }
    
    // Interface requirements
    public function getBounds(): array { /* ... */ }
    public function setVisible(bool $visible): self { /* ... */ }
    public function isVisible(): bool { /* ... */ }
}

// Use the widget
$widget = new MyWidget($display);
$widget->animate(fn($d, $p) => /* frame render */, 500, true);
$widget->withEffect(new ScrollingText());
```

## Architecture

```
src/
├── SSD1306Display.php           # Core display wrapper
├── Builder/                      # Display creation
│   ├── DisplayBuilder.php
│   └── DisplayFactory.php
├── Services/                     # High-level services
│   ├── TextRenderer.php
│   ├── ShapeRenderer.php
│   └── AnimationEngine.php
├── Math/                         # Mathematical utilities
│   ├── Vector2D.php
│   ├── Matrix2D.php
│   └── Curve.php
├── StateMachine/                 # State management
│   ├── DisplayState.php
│   ├── StateMachine.php
│   ├── Transition.php
│   └── States/
├── Effects/                      # Text effects
│   ├── TextEffect.php
│   ├── ScrollingText.php
│   ├── TypewriterText.php
│   ├── MarqueeText.php
│   └── FadeText.php
├── Shapes/                       # Shape objects
│   ├── ProgressBar.php
│   ├── Gauge.php
│   ├── RoundedBox.php
│   └── Icon.php
├── UI/                          # UI components
│   ├── Menu.php
│   ├── Notification.php
│   ├── Dashboard.php
│   ├── Widget.php
│   └── Widgets/
└── Concerns/                    # Traits & Interfaces
    ├── HasAnimations.php
    ├── HasEffects.php
    └── Renderable.php
```

## Testing

Run the test suite:

```bash
# All tests
./vendor/bin/pest

# Specific test suites
./vendor/bin/pest tests/Unit/
./vendor/bin/pest tests/Feature/

# With coverage
./vendor/bin/pest --coverage
```

## Examples

See [EXAMPLES.md](EXAMPLES.md) for complete usage scenarios including:
- System monitor dashboard
- Interactive menu system
- Animated splash screen
- Notification system
- State machine workflows

## Best Practices

### Resource Management

Always use a single `SSD1306Display` instance and pass it to services:

```php
// ✅ Good - Single instance
$display = DisplayFactory::standard('/dev/i2c-7');
$textRenderer = new TextRenderer($display);
$shapeRenderer = new ShapeRenderer($display);

// ❌ Bad - Multiple instances can cause segfaults
$display1 = new SSD1306Display(128, 32, '/dev/i2c-7');
$display2 = new SSD1306Display(128, 32, '/dev/i2c-7'); // Don't do this!
```

### Display Lifecycle

Always clear before rendering and display after drawing:

```php
$display->clearDisplay();
// ... draw operations ...
$display->display();
```

### Animation Timing

Use consistent delta time for smooth animations:

```php
$lastTime = microtime(true);
while (true) {
    $currentTime = microtime(true);
    $deltaTime = $currentTime - $lastTime;
    $lastTime = $currentTime;
    
    $stateMachine->update($deltaTime);
    $stateMachine->render();
}
```

## License

MIT License - See LICENSE file for details

## Credits

Built by Angel Gonzalez (@projectsaturnstudios)
Based on `php-ssd1306-i2c` and `php-adafruit-gfx` extensions

## Contributing

Contributions welcome! Please ensure:
- All tests pass on actual hardware
- New features include comprehensive Pest tests
- Code follows PSR-12 standards
- Documentation is updated

## Support

For issues, questions, or contributions, please visit the GitHub repository.

