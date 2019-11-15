<?php

namespace KABBOUCHI\LogsTool\Tests;

use Illuminate\Support\Facades\Route;
use KABBOUCHI\LogsTool\LogsToolServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);
    }

    protected function getPackageProviders($app)
    {
        return [
            LogsToolServiceProvider::class,
        ];
    }
}
