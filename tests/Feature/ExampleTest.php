<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_root_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }
}