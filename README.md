# lochmueller/http-range

Use PSR-7 messages and a PSR-15 handler/middlware to handle HTTP Range request and trigger valid partial download for streams/files. Hande multiple ranges and check different env requirements.

## Usage

    $handler = new HttpRangeRequestHandler(new LocalFileResource($filePath));
    $response = $handle->handle($serverRequest);
    // Use response header and content
    
    // or via middlware - HttpRangeMiddleware::class
    // response is used for the range split

## Tests

Run `composer test` to execute the current tests suite or run `composer code-fix` to format the code in dev context.

## Problems

- https://github.com/ramsey/http-range/issues/7
- Mixed dependencies in composer
- Support for https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Range