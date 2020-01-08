<?php


	namespace MehrIt\EliMiddlewareChain\Handler;


	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\MiddlewareInterface;
	use Psr\Http\Server\RequestHandlerInterface;
	use Psr\Http\Message\ResponseInterface;

	/**
	 * A container for middleware implementing the RequestHandlerInterface allowing easy chaining
	 */
	class DelegateHandler implements RequestHandlerInterface
	{
		protected $middleware;
		protected $next;

		/**
		 * Creates a new instance
		 * @param MiddlewareInterface $middleware The middleware
		 * @param RequestHandlerInterface $next The next handler or a resolver returning the next handler
		 */
		public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $next) {
			$this->middleware = $middleware;
			$this->next       = $next;
		}


		/**
		 * @inheritdoc
		 */
		public function handle(ServerRequestInterface $request): ResponseInterface {


			return $this->middleware->process($request, $this->next);
		}

	}