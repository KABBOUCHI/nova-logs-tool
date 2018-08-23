<?php

namespace KABBOUCHI\LogsTool\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use KABBOUCHI\LogsTool\LogsToolServiceProvider;

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
