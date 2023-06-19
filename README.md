# lochmueller/http-range

Use PSR-7 messages and a PSR-15 handler/middlware to handle HTTP Range request and trigger valid partial download for streams/files. Hande multiple ranges and check different env requirements.

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
- Mixed dependencies in composer
- Support for https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Range
- https://www.rfc-editor.org/rfc/rfc9110#field.range