# lochmueller/http-range

[![Latest Stable Version](https://poser.pugx.org/lochmueller/http-range/v/stable)](https://packagist.org/packages/lochmueller/http-range)
[![Total Downloads](https://poser.pugx.org/lochmueller/http-range/downloads)](https://packagist.org/packages/lochmueller/http-range)
[![License](https://poser.pugx.org/lochmueller/http-range/license)](https://packagist.org/packages/lochmueller/http-range)
[![Percentage of issues still open](https://isitmaintained.com/badge/open/lochmueller/http-range.svg)](https://isitmaintained.com/project/lochmueller/http-range "Percentage of issues still open")
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg?style=flat)](https://github.com/lochmueller/http-range/actions)

Use PSR-7 messages and a PSR-15 handler/middleware to handle HTTP Range request and trigger valid partial download for streams/files. Hande multiple ranges and check different env requirements.

## Usage

```php
use Lochmueller\HttpRange\HttpRangeRequestHandler;
use Lochmueller\HttpRange\Stream\ReadLocalFileStream;

$handler = new HttpRangeRequestHandler(new ReadLocalFileStream($filePath));
$response = $handler->handle($serverRequest);
// Use response header and content

// or via middleware - HttpRangeMiddleware::class
// response is used for the range split
```

## Tests

Run `composer test` to execute the current tests suite or run `composer code-fix` to format the code in dev context.

## Problems

- https://github.com/ramsey/http-range/issues/7
- https://www.rfc-editor.org/rfc/rfc9110#field.range