<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File; // Use the File facade
use App\Modules\ModuleRouteLoader;
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
        ])
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
 
    // // Load routes dynamically from modules
    // $modulesPath = base_path('app/Modules');
    
    // if (is_dir($modulesPath)) {
        
    //     // Scan the modules directory for subdirectories
    //     foreach (scandir($modulesPath) as $module) {
    //         if ($module === '.' || $module === '..') {
    //             continue; // Skip current and parent directory entries
    //         }
    
    //         // Path to the module's routes file
    //         $routeFile = "$modulesPath/$module/Routes/web.php";
    
    //         // Check if the routes file exists
    //         if (file_exists($routeFile)) {
    //             // Load the module's routes
    //             require $routeFile;
    //         }
    //     }
    // }
    // Load module routes
 
    return $app;