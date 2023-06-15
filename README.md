# http-range

Use PSR-7 messages and a PSR-15 to handle HTTP Range valid partial download requests to files. Hande multiple ranges and check different env requirements.

## Usage

    $handler = new RangeRequestHandler(new LocalFile($filePath));
    $response = $handle->handle($serverRequest);
    // Use response header and content

## Tests

Run `composer test` to execute the current tests suite.


## Problems

- https://github.com/ramsey/http-range/issues/7
- Mixed dependencies in composer