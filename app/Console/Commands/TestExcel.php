<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestExcel extends Command
{
    protected $signature = 'test:excel';

    public function handle()
    {
        $request = new Request();
        $controller = app(\App\Http\Controllers\MaterialController::class);
        $response = $controller->exportExcel($request);
        file_put_contents('test.xlsx', $response->getFile()->getContent());
        $this->info("Done Excel");
    }
}
