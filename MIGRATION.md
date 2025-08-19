# 🚀 PHP 7.x → PHP 8.0 Migration Guide

This project includes tools to **automatically detect and fix incompatibilities** when migrating from **PHP 7.x to PHP 8.0**.

## 🛠 Tools Used

- **[PHPCompatibility (with PHP_CodeSniffer)](https://github.com/PHPCompatibility/PHPCompatibility)**  
  Scans your code and reports **deprecated/removed functions, syntax, or APIs**.

- **[Rector](https://github.com/rectorphp/rector)**  
  Automated refactoring tool that **updates your code** to modern PHP syntax and APIs.

- **[PHPStan](https://github.com/phpstan/phpstan)**  
  Static analyzer that detects **type errors and potential runtime issues**.

---

## ⚡ Installation

Install the required dev dependencies:

```bash
composer require --dev \
    rector/rector \
    phpstan/phpstan \
    dealerdirect/phpcodesniffer-composer-installer \
    phpcompatibility/php-compatibility \
    squizlabs/php_codesniffer
```

# 📘 Laravel Migration Reference (8 → 12)

This document explains useful commands and tools when upgrading Laravel, including **Composer package compatibility**, **quick tests for conflicts**, and **official migration guides**.

---

## ⚡ Composer Commands

### 🔍 Check Incompatible Packages
To see **which packages block an upgrade** (e.g., upgrading `laravel/framework`):

```
composer why-not laravel/framework "12.*"

laravel/laravel         dev-main requires         laravel/framework (^8.75)
facade/flare-client-php 1.10.0   requires         illuminate/pipeline (^5.5|^6.0|^7.0|^8.0)
```
This means facade/flare-client-php blocks the upgrade to Laravel 12.
```
composer require laravel/framework:"^12.0" --with-all-dependencies
```
Composer will attempt to upgrade and resolve dependencies. add ```--dry-run```  flag to get the output without executed the command


## 📘 Official Laravel Upgrade Guides

Laravel provides step-by-step upgrade guides for each major release.
Always check these when migrating:

[Laravel 9 Upgrade Guide](https://laravel.com/docs/9.x/upgrade#updating-dependencies)

[Laravel 10 Upgrade Guide](https://laravel.com/docs/10.x/upgrade#updating-dependencies)

[Laravel 11 Upgrade Guide](https://laravel.com/docs/11.x/upgrade)

[Laravel 12 Upgrade Guide](https://laravel.com/docs/12.x/upgrade)

👉 Each guide explains breaking changes, deprecated features, and migration steps.


# Useful Tools for Migration

## Static Analyzers

[Larastan](https://github.com/larastan/larastan)
(Free)
Laravel extension for PHPStan → understands Eloquent models, facades, and collections.

[Shift](https://laravelshift.com/)
(Paid)
Automated Laravel upgrades (paid service that applies code changes to move between versions).
