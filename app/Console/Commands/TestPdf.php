<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestPdf extends Command
{
    protected $signature = 'test:pdf';

    public function handle()
    {
        $request = new Request();
        $controller = app(\App\Http\Controllers\MaterialController::class);
        $response = $controller->exportPdf($request);
        file_put_contents('test.pdf', $response->getContent());
        $this->info("Done");
    }
}
