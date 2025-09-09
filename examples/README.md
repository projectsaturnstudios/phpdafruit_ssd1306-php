# SSD1306-PHP Examples

This directory contains comprehensive examples demonstrating all features of the SSD1306-PHP library.

## 🚀 Quick Start

All examples can be run directly without Composer installation:

```bash
# Run any example directly
php examples/hello_extension.php
php examples/graphics_demo.php
php examples/system_stats.php
php examples/starfield.php
```

## 📁 Example Files

### `hello_extension.php` - Basic Display Usage
**Purpose**: Minimal example showing direct extension usage  
**Features**:
- Direct SSD1306 extension function calls
- Basic text display
- Simple pixel drawing
- Perfect for testing hardware connectivity

**Run**: `php examples/hello_extension.php`

### `graphics_demo.php` - Complete Graphics Showcase
**Purpose**: Comprehensive demonstration of all graphics capabilities  
**Features**:
- All drawing functions (pixels, lines, rectangles, circles)
- Text rendering with different sizes
- Display effects (contrast, invert, dim)
- Simple animation
- Pixel manipulation and reading
- Scrolling effects (horizontal and diagonal)
- Complex graphics composition

**Run**: `php examples/graphics_demo.php`

### `system_stats.php` - Real-time System Monitor
**Purpose**: Practical real-world application showing live data display  
**Features**:
- CPU usage monitoring
- Temperature reading (thermal zones)
- RAM usage statistics
- Disk usage statistics
- Continuous updates with signal handling
- Graceful shutdown with Ctrl+C

**Run**: `php examples/system_stats.php`

### `starfield.php` - Animated Starfield
**Purpose**: Entertainment and animation demonstration  
**Features**:
- 30 animated stars with variable speeds
- Smooth 25 FPS animation
- Signal handling for clean exit
- Optimized for 128x32 displays

**Run**: `php examples/starfield.php`

## 🔧 Hardware Requirements

All examples are designed for:
- **Yahboom CUBE case** with built-in SSD1306 display
- **I2C Bus 7** (Yahboom CUBE default)
- **Address 0x3C** (standard SSD1306)
- **128x32 pixel display** (can be adapted for 128x64)

## 🛠️ Customization

### Changing Display Settings

To adapt examples for different hardware configurations:

```php
// For different I2C bus (e.g., Raspberry Pi uses bus 1)
$display = new SSD1306(128, 32, 1, 0x3C);

// For different display size
$display = new SSD1306(128, 64, 7, 0x3C);

// For different I2C address
$display = new SSD1306(128, 32, 7, 0x3D);

// Enable debug output
$display = new SSD1306(debug: true);
```

### Adding Your Own Examples

When creating new examples, follow this pattern:

```php
<?php
declare(strict_types=1);

// Autoload - try Composer first, fallback to direct inclusion
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../src/SSD1306.php';
}

use ProjectSaturnStudios\SSD1306\SSD1306;

// Initialize display
$display = new SSD1306();
if (!$display->begin()) {
    die("Failed to initialize display\n");
}

// Your code here...

// Always cleanup
$display->end();
```

## 🐛 Troubleshooting Examples

### Display Not Working
1. **Check I2C bus**: Yahboom CUBE uses bus 7, not bus 1
2. **Verify extension**: `php -m | grep ssd1306`
3. **Test I2C**: `sudo i2cdetect -y 7`

### Permission Errors
```bash
# Add user to i2c group
sudo usermod -a -G i2c $USER
# Logout and login again
```

### Extension Not Found
```bash
# Verify extension is loaded
php -m | grep ssd1306

# Check if extension file exists
ls -la /usr/lib/php/*/ssd1306.so
```

## 📊 Performance Notes

- **Frame Rate**: Examples target ~25 FPS for smooth animation
- **I2C Speed**: Default I2C speed is sufficient for most applications
- **Memory Usage**: All examples use minimal memory footprint
- **CPU Usage**: Graphics operations are hardware-accelerated via C extension

## 🎯 Learning Path

**Recommended order for learning**:

1. **`hello_extension.php`** - Understand basic hardware communication
2. **`graphics_demo.php`** - Learn all drawing functions and effects
3. **`system_stats.php`** - See practical real-world application
4. **`starfield.php`** - Explore animation and optimization techniques

## 🔗 Integration Examples

These examples can be easily integrated into:
- **Laravel applications** (via service providers)
- **Symfony projects** (via services)
- **CLI tools** (direct usage)
- **IoT projects** (embedded PHP)
- **Monitoring systems** (like system_stats.php)

## 📝 Notes

- All examples include proper error handling
- Signal handling is implemented where appropriate (Ctrl+C support)
- Examples are optimized for the Yahboom CUBE hardware
- Code is well-commented for educational purposes
- Examples demonstrate both basic and advanced usage patterns

## 🚀 Next Steps

After running these examples:
1. Modify them to suit your hardware configuration
2. Combine features from different examples
3. Create your own applications using the patterns shown
4. Contribute new examples back to the project!

Happy coding with SSD1306-PHP! 🎉