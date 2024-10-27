<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use League\CommonMark\CommonMarkConverter;

class MarkdownRouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerMarkdownRoutes();
    }

    protected function registerMarkdownRoutes()
    {
        $files = File::files(resource_path('markdown'));
        $converter = new CommonMarkConverter();

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $route = str_replace('_', '-', $filename);
            
            Route::get($route, function () use ($filename, $converter) {
                $data = File::get(resource_path("markdown/{$filename}.md"));
                $content = $converter->convert($data);
                return view('layouts.markdown', [
                    'slot' => null,
                    'content' => $content,
                    'title' => ucwords(str_replace('_', ' ', $filename))
                ]);
            })->name("markdown.{$filename}");
        }
    }
}
