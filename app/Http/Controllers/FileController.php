<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    /**
     * Serve files from storage/app/public directory
     * This is a workaround for servers that don't allow symlink creation
     */
    public function show($path)
    {
        // Sanitize the path to prevent directory traversal
        $path = str_replace(['../', '..\\'], '', $path);
        
        // Check if file exists in public disk
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        // Get file path
        $filePath = Storage::disk('public')->path($path);
        
        // Get mime type
        $mimeType = Storage::disk('public')->mimeType($path);
        
        // Return file response
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
