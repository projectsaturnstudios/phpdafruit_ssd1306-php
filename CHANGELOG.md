# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-12-09

### Added
- Initial release of SSD1306-PHP intermediate library
- Complete object-oriented wrapper for SSD1306 PHP extension
- Support for all standard SSD1306 display sizes (128x64, 128x32, etc.)
- Comprehensive graphics API:
  - Pixel manipulation (`pixel()`, `getPixel()`)
  - Line drawing (`line()`)
  - Rectangle drawing (`rectangle()` with filled/outline options)
  - Circle drawing (`circle()` with filled/outline options)
  - Text rendering (`text()` with size and color control)
- Display control functions:
  - Display initialization (`begin()`)
  - Buffer management (`clear()`, `display()`)
  - Display effects (`invertDisplay()`, `dim()`, `setContrast()`)
  - Cleanup (`end()`)
- Scrolling effects:
  - Horizontal scrolling (left/right)
  - Diagonal scrolling (left/right)
  - Scroll control (`stopScroll()`)
- Hardware optimization for Yahboom CUBE case (I2C bus 7, address 0x3C)
- Comprehensive error handling and parameter validation
- Debug mode support for development and troubleshooting
- Full compatibility with Adafruit Python SSD1306 library API patterns

### Testing
- Complete test suite using Pest 4 framework
- Unit tests for all public methods and edge cases
- Feature tests for real-world usage scenarios
- Hardware-aware testing (skips when extension not available)
- Parameter validation testing
- Integration testing for complete display workflows

### Documentation
- Comprehensive README with installation instructions
- API reference documentation
- Hardware setup guide for Yahboom CUBE and manual wiring
- Troubleshooting guide with common issues and solutions
- Example files demonstrating all major features
- Testing guide (TESTING.md) with detailed test information

### Examples
- `hello_extension.php` - Basic display initialization and text
- `system_stats.php` - Real-time system information display
- `starfield.php` - Animated starfield demonstration

### Dependencies
- PHP 8.1+ requirement
- SSD1306 PHP extension dependency
- Pest 4 for testing (dev dependency)

### Architecture
- Clean separation between PHP API and C extension
- PSR-4 autoloading support
- Composer package structure
- MIT license for open source compatibility

## [Unreleased]

### Planned
- Additional display size presets
- Enhanced text rendering with custom fonts
- Image/bitmap display support
- Performance optimizations for rapid updates
- Additional scrolling effects and animations
- Integration examples with popular PHP frameworks

---

## Version History

- **1.0.0** - Initial stable release with complete SSD1306 API coverage