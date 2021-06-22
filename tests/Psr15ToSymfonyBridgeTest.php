<?php

namespace TheCodingMachine\Tests\Psr15Bridge;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use TheCodingMachine\Psr15Bridge\Psr15ToSymfonyBridge;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class Psr15ToSymfonyBridgeTest extends TestCase
{
    public function testProcess()
    {
        // Symfony middleware that returns 'foo'
        $symfonyMiddleware = new class implements HttpKernelInterface
         {
             public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
             {
                 return new SymfonyResponse('foo');
             }
         };

        $delegate = $this->createMock(RequestHandlerInterface::class);

        $bridge = new Psr15ToSymfonyBridge($symfonyMiddleware);

        $request = new ServerRequest([], [], new Uri('/'), 'GET');
        $response = $bridge->process($request, $delegate);

        $this->assertEquals('foo', (string) $response->getBody());
    }
}
