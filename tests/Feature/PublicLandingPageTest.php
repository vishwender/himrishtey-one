<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicLandingPageTest extends TestCase
{
    public function test_public_landing_page_is_available_before_authentication(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Meaningful matches.')
            ->assertSee('Staff sign in')
            ->assertSee(route('admin.login'), escape: false);
    }
}
