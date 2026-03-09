<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image
{
    /**
     * Upload an image to the public storage disk.
     */
    public static function uploadToPublic($file, $folder = 'uploads')
    {
        if (!$file) return null;

        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $fileName, 'public');

        return Storage::disk('public')->url($path);
    }
}
