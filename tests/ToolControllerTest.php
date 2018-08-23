<?php

namespace KABBOUCHI\LogsTool\Tests;

use KABBOUCHI\LogsTool\Http\Controllers\LogsController;
use KABBOUCHI\LogsTool\LogsTool;
use Symfony\Component\HttpFoundation\Response;

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
