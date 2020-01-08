<?php


	namespace MehrItMiddlewareChainTest\Unit\Handler;



	use MehrIt\EliMiddlewareChain\Handler\DelegateHandler;
	use MehrItMiddlewareChainTest\Unit\TestCase;
	use PHPUnit\Framework\MockObject\MockObject;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\MiddlewareInterface;
	use Psr\Http\Server\RequestHandlerInterface;

	class DelegateHandlerTest extends TestCase
	{
		public function testHandle() {

			/** @var ServerRequestInterface|MockObject $request */
			$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

			/** @var ResponseInterface|MockObject $response */
			$response = $this->getMockBuilder(ResponseInterface::class)->getMock();

			/** @var RequestHandlerInterface|MockObject $handler */
			$handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

			/** @var MiddlewareInterface|MockObject $middleware */
			$middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
			$middleware->expects($this->once())
				->method('process')
				->with($request, $handler)
				->willReturn($response);


			$delegate = new DelegateHandler($middleware, $handler);

			$this->assertSame($response, $delegate->handle($request));
		}

	}