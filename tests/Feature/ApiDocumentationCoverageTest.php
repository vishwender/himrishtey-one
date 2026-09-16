<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\ApiDocumentationController;
use Tests\TestCase;

class ApiDocumentationCoverageTest extends TestCase
{
    public function test_every_registered_api_route_has_documentation_details(): void
    {
        $view = app(ApiDocumentationController::class)->index();
        $endpoints = $view->getData()['endpointGroups']->flatten(1);

        $this->assertNotEmpty($endpoints);

        foreach ($endpoints as $endpoint) {
            $this->assertIsArray(
                $endpoint['details'],
                "Missing documentation for {$endpoint['methods']->join('|')} {$endpoint['uri']}"
            );
            $this->assertNotEmpty($endpoint['details']['description']);
            $this->assertArrayHasKey('request', $endpoint['details']);
            $this->assertArrayHasKey('response', $endpoint['details']);
            $this->assertArrayHasKey('notes', $endpoint['details']);
            foreach ($endpoint['methods'] as $method) {
                $example = $endpoint['curl_examples'][$method];
                $this->assertStringContainsString(url($endpoint['uri']), $example);
                $this->assertStringContainsString('Accept: application/json', $example);
                $this->assertStringContainsString('X-App-Code: himrishtey', $example);
                $this->assertSame($endpoint['authenticated'], str_contains($example, 'Authorization: Bearer <token>'));
            }
        }
    }

    public function test_curl_examples_use_the_correct_request_encoding(): void
    {
        $endpoints = app(ApiDocumentationController::class)->index()->getData()['endpointGroups']->flatten(1);
        $example = fn (string $uri, string $method): string => $endpoints
            ->first(fn (array $endpoint): bool => $endpoint['uri'] === '/api/v1/'.$uri && $endpoint['methods']->contains($method))['curl_examples'][$method];

        $login = $example('auth/login', 'POST');
        $this->assertStringContainsString('Content-Type: application/json', $login);
        $this->assertStringContainsString('--data-raw', $login);
        $this->assertStringContainsString('"login": "HIM12345"', $login);

        $search = $example('search/quick', 'GET');
        $this->assertStringContainsString('--get', $search);
        $this->assertStringContainsString("--data-urlencode 'marital_status=Never Married'", $search);
        $this->assertStringNotContainsString('--data-raw', $search);

        $upload = $example('success-stories/{id}', 'PUT');
        $this->assertStringContainsString('--request POST', $upload);
        $this->assertStringContainsString("--form-string '_method=PUT'", $upload);
        $this->assertStringContainsString("--form 'photo=@/path/to/photo.jpg'", $upload);
        $this->assertStringNotContainsString('Content-Type:', $upload);

        $empty = $example('memberships/callback-request', 'POST');
        $this->assertStringNotContainsString('--data', $empty);
        $this->assertStringNotContainsString('Content-Type:', $empty);
    }
}
