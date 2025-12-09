<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use Jhonoryza\Ipwhitelist\WhitelistIpMiddleware;

class WhitelistIpMiddlewareTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // Set up a test whitelist
        $app['config']->set('ipwhitelist.ip_whitelist', '127.0.0.1,192.168.1.1');
    }

    public function test_allows_whitelisted_ip()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $middleware = new WhitelistIpMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_allows_whitelisted_ip_has_spaces()
    {
        $this->app['config']->set('ipwhitelist.ip_whitelist', ' 127.0.0.1, 192.168.1.1');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $middleware = new WhitelistIpMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_allows_whitelisted_ip_with_client_ip_has_spaces()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => ' 127.0.0.1 ']);
        $middleware = new WhitelistIpMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_blocks_non_whitelisted_ip_with_json()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '10.0.0.1']);
        $request->headers->set('Accept', 'application/json');
        $middleware = new WhitelistIpMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Forbidden', $response->getContent());
        $this->assertStringContainsString('10.0.0.1', $response->getContent());
    }

    public function test_blocks_non_whitelisted_ip_with_html()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Forbidden: Your IP: 10.0.0.2 is not allowed.');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '10.0.0.2']);
        $middleware = new WhitelistIpMiddleware();

        // Should throw HttpException for non-JSON requests
        $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });
    }
}
