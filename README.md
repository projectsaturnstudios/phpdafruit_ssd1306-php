# SSD1306-PHP

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Tests](https://img.shields.io/badge/tests-pest-green.svg)](https://pestphp.com)

A comprehensive PHP library for controlling SSD1306 OLED displays, providing an elegant object-oriented interface that mirrors the popular Adafruit Python SSD1306 library. This intermediate package wraps the [SSD1306 PHP extension](https://github.com/projectsaturnstudios/adafruit_ssd1306-php) to provide a clean, modern PHP API.

## 🚀 Features

- **🎨 Complete Graphics API**: Pixels, lines, rectangles, circles, and text rendering
- **📱 Multiple Display Sizes**: Support for 128x64, 128x32, and other SSD1306 variants
- **🔄 Scrolling Effects**: Horizontal and diagonal scrolling animations
- **⚡ Hardware Optimized**: Direct I2C communication via native C extension
- **🧪 Fully Tested**: Comprehensive test suite with Pest 4
- **🐛 Debug Support**: Built-in debugging and error handling
- **🔌 Yahboom CUBE Ready**: Optimized for Jetson Orin Nano CUBE case (I2C bus 7)

## 📋 Requirements

- **PHP 8.1+** with development headers
- **[SSD1306 PHP Extension](https://github.com/projectsaturnstudios/adafruit_ssd1306-php)** (must be installed first)
- **NVIDIA Jetson Orin** or compatible Linux system with I2C support
- **I2C Tools** (`sudo apt install i2c-tools`)

## 📦 Installation

### Step 1: Install the SSD1306 PHP Extension

First, install the required C extension:

```bash
# Install the SSD1306 PHP extension (see its README for details)
git clone https://github.com/projectsaturnstudios/adafruit_ssd1306-php.git
cd adafruit_ssd1306-php
phpize && ./configure --enable-ssd1306 && make && sudo make install

# Add to php.ini
echo "extension=ssd1306.so" | sudo tee -a /etc/php/8.4/cli/php.ini
```

### Step 2: Install This Package

```bash
composer require projectsaturnstudios/ssd1306-php
```

## 🚀 Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use ProjectSaturnStudios\SSD1306\SSD1306;

// Initialize display (Yahboom CUBE: bus 7, address 0x3C, 128x32)
$display = new SSD1306(128, 32, 7, 0x3C);

if (!$display->begin()) {
    die("Failed to initialize display\n");
}

// Clear screen and draw some graphics
$display->clear();
$display->rectangle(10, 5, 50, 20, SSD1306::WHITE);
$display->circle(90, 15, 10, SSD1306::WHITE, true);
$display->text("Hello PHP!", 0, 25);

// Update the display
$display->display();

// Keep it visible
sleep(5);

// Cleanup
$display->end();
```

## 📚 API Reference

### Display Management

```php
// Constructor
$display = new SSD1306(int $width = 128, int $height = 32, int $i2cBus = 7, int $i2cAddress = 0x3C, bool $debug = false);

// Initialize display
bool $display->begin()

// Clear display buffer
void $display->clear()

// Update display with buffer contents
void $display->display()

// Cleanup display
void $display->end()
```

### Drawing Functions

```php
// Draw single pixel
$display->pixel(int $x, int $y, int $color = SSD1306::WHITE)

// Draw line
$display->line(int $x0, int $y0, int $x1, int $y1, int $color = SSD1306::WHITE)

// Draw rectangle (filled or outline)
$display->rectangle(int $x, int $y, int $width, int $height, int $color = SSD1306::WHITE, bool $filled = false)

// Draw circle (filled or outline)
$display->circle(int $x, int $y, int $radius, int $color = SSD1306::WHITE, bool $filled = false)

// Draw text
$display->text(string $text, int $x, int $y, int $size = 1, int $color = SSD1306::WHITE)
```

### Display Properties

```php
// Get dimensions
int $display->getWidth()
int $display->getHeight()

// Get pixel value
int $display->getPixel(int $x, int $y)

// Display effects
$display->invertDisplay(bool $invert)
$display->dim(bool $dim)
$display->setContrast(int $contrast) // 0-255
```

### Scrolling Effects

```php
// Horizontal scrolling
$display->startScrollRight(int $startPage, int $endPage)
$display->startScrollLeft(int $startPage, int $endPage)

// Diagonal scrolling
$display->startScrollDiagRight(int $startPage, int $endPage)
$display->startScrollDiagLeft(int $startPage, int $endPage)

// Stop scrolling
$display->stopScroll()
```

### Constants

```php
// Colors
SSD1306::BLACK  // 0
SSD1306::WHITE  // 1

// Default dimensions
SSD1306::WIDTH      // 128
SSD1306::HEIGHT_32  // 32
SSD1306::HEIGHT_64  // 64

// Default I2C settings (Yahboom CUBE)
SSD1306::DEFAULT_I2C_BUS     // 7
SSD1306::DEFAULT_I2C_ADDRESS // 0x3C
```

## 🎯 Examples

The `examples/` directory contains complete demonstrations:

### Basic Extension Usage
```bash
php examples/hello_extension.php
```

### System Statistics Display
```bash
php examples/system_stats.php
```

### Animated Starfield Demo
```bash
php examples/starfield.php
```

## 🧪 Testing

This package includes comprehensive tests using Pest 4:

```bash
# Run all tests
./vendor/bin/pest

# Run with coverage
./vendor/bin/pest --coverage

# Run specific test suites
./vendor/bin/pest tests/Unit
./vendor/bin/pest tests/Feature
```

Tests automatically skip when the SSD1306 extension is not available, making them safe for CI environments.

See [TESTING.md](TESTING.md) for detailed testing information.

## 🔧 Hardware Setup

### Yahboom CUBE Case (Recommended)

This library is optimized for the [Yahboom Jetson Nano CUBE case](https://www.yahboom.net/study/CUBE_NANO):
- Built-in SSD1306 OLED display (128x32)
- Pre-wired I2C connections on **bus 7** (not bus 1!)
- Perfect form factor for Jetson Orin Nano/NX

### Manual SSD1306 Wiring

For standalone displays:
- **VCC**: 3.3V or 5V
- **GND**: Ground  
- **SCL**: I2C Clock (GPIO 3)
- **SDA**: I2C Data (GPIO 2)

### I2C Detection

```bash
# List I2C buses
i2cdetect -l

# Scan for devices (Yahboom CUBE uses bus 7)
sudo i2cdetect -y 7

# Look for device at 0x3C or 0x3D
```

## 🐛 Troubleshooting

### Display Not Working
1. **Check I2C bus**: Yahboom CUBE uses bus 7, not bus 1
2. **Verify extension**: `php -m | grep ssd1306`
3. **Test I2C**: `sudo i2cdetect -y 7`
4. **Check permissions**: Add user to `i2c` group

### Extension Not Found
```bash
# Verify extension is installed
php -m | grep ssd1306

# Check php.ini
php --ini | grep ssd1306
```

### Permission Denied
```bash
# Add user to i2c group
sudo usermod -a -G i2c $USER
# Logout and login again
```

## 🏗️ Architecture

This package provides a **clean intermediate layer** between PHP applications and the SSD1306 hardware:

```
PHP Application
       ↓
SSD1306-PHP (this package) ← Object-oriented API
       ↓
SSD1306 C Extension ← Direct hardware access
       ↓
Linux I2C Drivers ← Kernel-level communication
       ↓
SSD1306 Hardware ← Physical OLED display
```

## 🤝 Integration

This package integrates seamlessly with:
- **[CubeNanoLib](https://github.com/projectsaturnstudios/cubenanolib)** - Complete Yahboom CUBE control
- **Laravel Applications** - Via service providers and facades
- **MCP Servers** - For AI-assisted hardware control

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Adafruit Industries** - For the original Python SSD1306 library design
- **Yahboom Technology** - For the excellent CUBE case hardware
- **PHP Extension Community** - For guidance on extension development

## 🚀 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/projectsaturnstudios/ssd1306/issues)
- **Email**: info@projectsaturnstudios.com
- **Documentation**: See examples and tests for usage patterns

---

**Project Saturn Studios, LLC** - Building the future of embedded PHP development.