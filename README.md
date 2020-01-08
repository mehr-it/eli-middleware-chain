# PSR middleware chain
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mehr-it/eli-middleware-chain.svg?style=flat-square)](https://packagist.org/packages/mehr-it/eli-middleware-chain)
[![Build Status](https://travis-ci.org/mehr-it/eli-middleware-chain.svg?branch=master)](https://travis-ci.org/mehr-it/eli-middleware-chain)

When defining a request processing chain, usually more than one middleware is involved into the
processing chain. The `ChainHandler` allows to define the PSR-15 middleware processing stack as 
array or iterator:

    $chain = new ChainHandler([
          new MiddlewareA(),
          new MiddlewareB(),
    ], $next); 

This makes code much more readable and allows easy dynamic configuration of the middleware chain.

To create middleware instances on the fly - only when needed - resolver functions may be used:

    $chain = new ChainHandler([
          function() { return new MiddlewareA(); },
          function() { return new MiddlewareB(); },
    ], $next); 

## Middleware instead of handler

Sometimes a middleware chain is required as middleware itself. The `ChainMiddleware` can be used
for such purposes. It's usage is straightforward as the `ChainHandler`:

    $chain = new ChainMiddleware([
          new MiddlewareA(),
          new MiddlewareB(),
    ]); 
