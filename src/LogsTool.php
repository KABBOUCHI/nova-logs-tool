<?php

namespace KABBOUCHI\LogsTool;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class LogsTool extends BaseTool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('LogsTool', __DIR__.'/../dist/js/tool.js');
        Nova::style('LogsTool', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('LogsTool::navigation');
    }
}
