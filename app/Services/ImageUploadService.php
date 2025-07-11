<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImageUploadService
{
    public static function saveWithWebp(
        UploadedFile $file,
        string $dir = '',
        ?int $width = null,
        ?int $height = null,
        ?int $quality = null,
        string $disk = ''
    ): string {
        // Gera nome aleatório
        $filename = Str::uuid()->toString() . '.webp';

        // Caminho onde será salvo
        $path = $dir . '/' . $filename;

        // Cria instância do ImageManager (v3)
        $manager = new ImageManager(new GdDriver());

        // Cria e trata a imagem
        $image = $manager->read($file->getRealPath())
            ->scaleDown(width: $width, height: $height)
            ->toWebp(quality: $quality);

        // Salva no disco do Laravel
        Storage::disk($disk)->put($path, (string) $image->toString());

        return $path;
    }
}
