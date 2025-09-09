# Contributing to SSD1306-PHP

Thank you for your interest in contributing to SSD1306-PHP! This document provides guidelines and information for contributors.

## 🚀 Getting Started

### Prerequisites

- PHP 8.1 or higher
- [SSD1306 PHP Extension](https://github.com/projectsaturnstudios/adafruit_ssd1306-php) installed
- Composer for dependency management
- Git for version control

### Development Setup

1. **Fork and clone the repository**:
   ```bash
   git clone https://github.com/your-username/ssd1306.git
   cd ssd1306
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Install the SSD1306 extension** (required for testing):
   ```bash
   # See the extension repository for installation instructions
   # https://github.com/projectsaturnstudios/adafruit_ssd1306-php
   ```

4. **Run tests to verify setup**:
   ```bash
   composer test
   ```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test suites
composer test-unit
composer test-feature

# Run tests with verbose output
./vendor/bin/pest --verbose
```

### Writing Tests

- All new features must include tests
- Tests should follow the existing patterns in `tests/`
- Use descriptive test names and organize with `describe()` blocks
- Tests automatically skip when the SSD1306 extension is not available

Example test structure:
```php
describe('New Feature', function () {
    beforeEach(function () {
        if (!extension_loaded('ssd1306')) {
            $this->markTestSkipped('SSD1306 extension not available');
        }
    });

    it('does something specific', function () {
        $display = new SSD1306();
        expect($display->begin())->toBeTrue();
        
        // Test your feature
        
        $display->end();
    });
});
```

## 📝 Code Style

### PHP Standards

- Follow **PSR-12** coding standards
- Use **strict types**: `declare(strict_types=1);`
- Use **type hints** for all parameters and return types
- Write **PHPDoc comments** for all public methods

### Code Organization

- Keep classes focused and single-purpose
- Use meaningful variable and method names
- Add comments for complex logic
- Maintain backward compatibility when possible

### Example Code Style

```php
<?php

declare(strict_types=1);

namespace ProjectSaturnStudios\SSD1306;

/**
 * Example class demonstrating code style
 */
class ExampleClass
{
    /**
     * Example method with proper documentation
     *
     * @param int $value The input value
     * @return bool Success status
     * @throws InvalidArgumentException If value is invalid
     */
    public function exampleMethod(int $value): bool
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be non-negative');
        }
        
        return true;
    }
}
```

## 🐛 Bug Reports

When reporting bugs, please include:

1. **PHP version** and extension version
2. **Hardware details** (I2C bus, display size, etc.)
3. **Minimal code example** that reproduces the issue
4. **Expected vs actual behavior**
5. **Error messages** or stack traces

Use the GitHub issue template when available.

## ✨ Feature Requests

For new features:

1. **Check existing issues** to avoid duplicates
2. **Describe the use case** and why it's needed
3. **Provide examples** of how it would be used
4. **Consider backward compatibility**

## 🔄 Pull Request Process

### Before Submitting

1. **Create an issue** first for significant changes
2. **Fork the repository** and create a feature branch
3. **Write tests** for your changes
4. **Update documentation** if needed
5. **Run the test suite** to ensure nothing breaks

### Pull Request Guidelines

1. **Use descriptive titles** and descriptions
2. **Reference related issues** with "Fixes #123" or "Closes #123"
3. **Keep changes focused** - one feature/fix per PR
4. **Include tests** for new functionality
5. **Update CHANGELOG.md** for significant changes

### Branch Naming

Use descriptive branch names:
- `feature/add-bitmap-support`
- `bugfix/fix-scrolling-issue`
- `docs/update-readme`

### Commit Messages

Follow conventional commit format:
- `feat: add bitmap display support`
- `fix: resolve scrolling animation bug`
- `docs: update API documentation`
- `test: add coverage for circle drawing`

## 📚 Documentation

### Code Documentation

- **PHPDoc comments** for all public methods
- **Inline comments** for complex logic
- **Type hints** for all parameters and returns

### User Documentation

- Update **README.md** for new features
- Add **examples** for new functionality
- Update **API reference** sections
- Keep **CHANGELOG.md** current

## 🏗️ Architecture Guidelines

### Design Principles

1. **Simplicity**: Keep the API simple and intuitive
2. **Consistency**: Follow established patterns
3. **Performance**: Optimize for embedded/IoT use cases
4. **Reliability**: Handle errors gracefully
5. **Compatibility**: Maintain backward compatibility

### Extension Integration

- This library wraps the SSD1306 C extension
- Avoid duplicating extension functionality
- Add value through object-oriented design
- Handle extension errors gracefully

## 🤝 Community Guidelines

### Code of Conduct

- Be respectful and inclusive
- Help others learn and grow
- Focus on constructive feedback
- Celebrate contributions of all sizes

### Communication

- Use GitHub issues for bug reports and feature requests
- Join discussions in pull requests
- Ask questions if anything is unclear
- Share your use cases and experiences

## 🏷️ Release Process

### Versioning

We follow [Semantic Versioning](https://semver.org/):
- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

### Release Checklist

1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Run full test suite
4. Create GitHub release with notes
5. Tag the release

## 📞 Getting Help

- **GitHub Issues**: For bugs and feature requests
- **GitHub Discussions**: For questions and community chat
- **Email**: info@projectsaturnstudios.com for private matters

## 🙏 Recognition

Contributors are recognized in:
- GitHub contributors list
- CHANGELOG.md for significant contributions
- README.md acknowledgments section

Thank you for contributing to SSD1306-PHP! Your efforts help make embedded PHP development more accessible and powerful. 🚀