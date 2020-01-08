<?php


	namespace MehrIt\EliMiddlewareChain\Middleware;


	use Iterator;
	use MehrIt\EliMiddlewareChain\Handler\ChainHandler;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\MiddlewareInterface;
	use Psr\Http\Server\RequestHandlerInterface;

	/**
	 * Implements a request handling middleware chain as new middleware
	 * @package MehrIt\EliMiddlewareChain\Middleware
	 */
	class ChainMiddleware implements MiddlewareInterface
	{

		/**
		 * @var MiddlewareInterface[]
		 */
		protected $middleware;

		/**
		 * Creates a new instance
		 * @param MiddlewareInterface[]|Iterator|callable[] $middleware The middleware stack (resolver functions are also possible)
		 */
		public function __construct(iterable $middleware) {
			$this->middleware = $middleware;
		}


		/**
		 * @inheritDoc
		 */
		public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

			return (new ChainHandler($this->middleware, $handler))
				->handle($request);
		}


	}