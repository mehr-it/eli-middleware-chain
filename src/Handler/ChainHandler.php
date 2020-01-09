<?php


	namespace MehrIt\EliMiddlewareChain\Handler;


	use ArrayIterator;
	use Iterator;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\MiddlewareInterface;
	use Psr\Http\Server\RequestHandlerInterface;

	/**
	 * Implements a request handling chain with multiple middleware chained before the request handler
	 * @package MehrIt\EliMiddlewareChain\Handler
	 */
	class ChainHandler implements RequestHandlerInterface
	{
		/**
		 * @var Iterator|MiddlewareInterface[]|callable
		 */
		protected $middlewareStack;

		/**
		 * @var RequestHandlerInterface
		 */
		protected $handler;

		/**
		 * @var bool
		 */
		protected $first = true;

		/**
		 * Creates a new instance
		 * @param MiddlewareInterface[]|Iterator|callable[] $middleware The middleware stack (resolver functions are also possible)
		 * @param RequestHandlerInterface $handler The handler
		 */
		public function __construct(iterable $middleware, RequestHandlerInterface $handler) {

			if (!($middleware instanceof Iterator))
				$middleware = new ArrayIterator($middleware);

			$this->middlewareStack = $middleware;
			$this->handler         = $handler;
		}


		/**
		 * @inheritdoc
		 */
		public function handle(ServerRequestInterface $request): ResponseInterface {

			$stack = $this->middlewareStack;

			// move iterator (except for first call)
			if (!$this->first)
				$stack->next();
			else
				$this->first = false;

			if ($stack->valid()) {
				$curr = $stack->current();

				// call resolver if no middleware instance given
				if (!($curr instanceof MiddlewareInterface))
					$curr = call_user_func($curr);

				return $curr->process($request, $this);
			}
			else {
				return $this->handler->handle($request);
			}
		}


	}