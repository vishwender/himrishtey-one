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
        }
    }
}
