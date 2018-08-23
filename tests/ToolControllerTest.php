<?php

namespace KABBOUCHI\LogsTool\Tests;

class ToolControllerTest extends TestCase
{
    /** @test */
    public function it_can_can_return_a_response()
    {
        $this
            ->get('nova-vendor/KABBOUCHI/nova-logs-tool/endpoint')
            ->assertSuccessful();
    }
}
