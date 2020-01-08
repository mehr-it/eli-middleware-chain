<?php


	namespace MehrItMiddlewareChainTest\Unit\Handler;


	use ArrayIterator;
	use EmptyIterator;
	use MehrIt\EliMiddlewareChain\Handler\ChainHandler;
	use MehrItMiddlewareChainTest\Unit\TestCase;
	use PHPUnit\Framework\MockObject\MockObject;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\MiddlewareInterface;
	use Psr\Http\Server\RequestHandlerInterface;


	class ChainHandlerTest extends TestCase
	{
		public function testWithoutMiddleware() {
			/** @var ServerRequestInterface|MockObject $request */
			$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response */
			$response = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request)
				->willReturn($response);

			$chain = new ChainHandler([], $handler);

			$this->assertSame($response, $chain->handle($request));

		}

		public function testWithoutMiddleware_iterator() {
			/** @var ServerRequestInterface|MockObject $request */
			$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response */
			$response = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request)
				->willReturn($response);

			$chain = new ChainHandler(new EmptyIterator(), $handler);

			$this->assertSame($response, $chain->handle($request));

		}

		public function testWithoutMiddleware_generator() {
			/** @var ServerRequestInterface|MockObject $request */
			$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response */
			$response = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request)
				->willReturn($response);

			$gen = function ()  {
				$arr = [];

				foreach ($arr as $curr) {
					yield $curr;
				}
			};

			$chain = new ChainHandler($gen(), $handler);

			$this->assertSame($response, $chain->handle($request));

		}

		public function testOneMiddleware() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request2)
				->willReturn($response2);

			$chain = new ChainHandler([$m1], $handler);


			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testOneMiddleware_iterator() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request2)
				->willReturn($response2);

			$chain = new ChainHandler(new ArrayIterator([$m1]), $handler);


			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testOneMiddleware_resolverFunction() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request2)
				->willReturn($response2);

			$chain = new ChainHandler(new ArrayIterator([function() use ($m1) {return $m1; }]), $handler);


			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testOneMiddleware_generator() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request2)
				->willReturn($response2);

			$gen = function () use ($m1) {
				$arr = [$m1];

				foreach ($arr as $curr) {
					yield $curr;
				}
			};

			$chain = new ChainHandler($gen(), $handler);

			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testMultipleMiddleware() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			/** @var ServerRequestInterface|MockObject $request3 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request3 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			/** @var ResponseInterface|MockObject $response3 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response3 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var MiddlewareInterface|MockObject $m1 */
			$m2 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m2->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request2, $request3, $response2, $response3) {
					$this->assertSame($request2, $request);

					$this->assertSame($response3, $next->handle($request3));

					return $response2;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request3)
				->willReturn($response3);

			$chain = new ChainHandler([$m1, $m2], $handler);

			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testMultipleMiddleware_iterator() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			/** @var ServerRequestInterface|MockObject $request3 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request3 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			/** @var ResponseInterface|MockObject $response3 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response3 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var MiddlewareInterface|MockObject $m1 */
			$m2 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m2->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request2, $request3, $response2, $response3) {
					$this->assertSame($request2, $request);

					$this->assertSame($response3, $next->handle($request3));

					return $response2;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request3)
				->willReturn($response3);

			$chain = new ChainHandler(new ArrayIterator([$m1, $m2]), $handler);

			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testMultipleMiddleware_generator() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			/** @var ServerRequestInterface|MockObject $request3 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request3 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			/** @var ResponseInterface|MockObject $response3 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response3 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var MiddlewareInterface|MockObject $m1 */
			$m2 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m2->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request2, $request3, $response2, $response3) {
					$this->assertSame($request2, $request);

					$this->assertSame($response3, $next->handle($request3));

					return $response2;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request3)
				->willReturn($response3);

			$gen = function () use ($m1, $m2) {
				$arr = [$m1, $m2];

				foreach ($arr as $curr) {
					yield $curr;
				}
			};

			$chain = new ChainHandler($gen(), $handler);

			$this->assertSame($response1, $chain->handle($request1));

		}

		public function testMultipleMiddleware_resolverFunction() {

			/** @var ServerRequestInterface|MockObject $request1 */
			/** @var ServerRequestInterface|MockObject $request2 */
			/** @var ServerRequestInterface|MockObject $request3 */
			$request1 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request2 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
			$request3 = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response1 */
			/** @var ResponseInterface|MockObject $response2 */
			/** @var ResponseInterface|MockObject $response3 */
			$response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();
			$response3 = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $m1 */
			$m1 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m1->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request1, $request2, $response1, $response2) {
					$this->assertSame($request1, $request);

					$this->assertSame($response2, $next->handle($request2));

					return $response1;
				});

			/** @var MiddlewareInterface|MockObject $m1 */
			$m2 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$m2->expects($this->once())
				->method('process')
				->with($request1)
				->willReturnCallback(function ($request, RequestHandlerInterface $next) use ($request2, $request3, $response2, $response3) {
					$this->assertSame($request2, $request);

					$this->assertSame($response3, $next->handle($request3));

					return $response2;
				});

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
			$handler->expects($this->once())
				->method('handle')
				->with($request3)
				->willReturn($response3);

			$chain = new ChainHandler(new ArrayIterator([
				function () use ($m1) {
					return $m1;
				},
				function () use ($m2) {
					return $m2;
				}
			]), $handler);

			$this->assertSame($response1, $chain->handle($request1));

		}
	}