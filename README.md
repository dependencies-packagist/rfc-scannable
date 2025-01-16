# RFC Scannable

Implement a scanner that can scan by namespace or path, etc.

[![GitHub Tag](https://img.shields.io/github/v/tag/dependencies-packagist/rfc-scannable)](https://github.com/dependencies-packagist/rfc-scannable/tags)
[![Total Downloads](https://img.shields.io/packagist/dt/rfc/scannable?style=flat-square)](https://packagist.org/packages/rfc/scannable)
[![Packagist Version](https://img.shields.io/packagist/v/rfc/scannable)](https://packagist.org/packages/rfc/scannable)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/rfc/scannable)](https://github.com/dependencies-packagist/rfc-scannable)
[![Packagist License](https://img.shields.io/github/license/dependencies-packagist/rfc-scannable)](https://github.com/dependencies-packagist/rfc-scannable)

## Installation

You can install the package via [Composer](https://getcomposer.org/):

```bash
composer require rfc/scannable
```

## Usage

```php
use Rfc\Scannable\Scan;
use Rfc\Scannable\ScanFile;
use Rfc\Scannable\ScanNamespace;
use Rfc\Scannable\ScanPath;
use Rfc\Scannable\ScanPackageNamespace;

public function scan(): void
{
    // ScanPackageNamespace
    $scan = new ScanPackageNamespace(['GuzzleHttp']);
    
    // ScanNamespace
    $scan = new ScanNamespace(['Illuminate\Support\Arr']);
    $scan = new ScanNamespace(['Illuminate\Support*']);
    
    // ScanPath
    $scan = new ScanPath(__DIR__.'/../Http/');
    $scan = new ScanPath(new \RecursiveDirectoryIterator(__DIR__.'/../Http/Controllers'));
    
    // ScanFile
    $scan = new ScanFile(__FILE__);
    $scan = new ScanFile(new \SplFileInfo(__DIR__ . '/AppServiceProvider.php'));
    
    // Scan
    $scan = new Scan('Illuminate\Support\Arr');
    $scan = new Scan(['Illuminate\Support\Arr']);
    $scan = new Scan(new \ReflectionClass('Illuminate\Support\Arr'));
}
```

## License

Nacosvel Contracts is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
