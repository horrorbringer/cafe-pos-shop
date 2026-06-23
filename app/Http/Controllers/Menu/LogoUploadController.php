<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoUploadController extends Controller
{
    public function __invoke(Request $request): array
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:1024'],
        ]);

        $path = $request->file('file')->store('menu-logo', 'public');

        return [
            'path' => $path,
            'full_path' => $path,
            'url' => Storage::url($path),
        ];
    }
}
