<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Filesystem\FilesystemAdapter;

class ImageService
{
    protected FilesystemAdapter $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Faz upload da imagem com nome único e registra log
     */
    public function uploadImage(UploadedFile $image, string $path, ?string $userId = null)
    {
        $filename = uniqid("user_{$userId}_") . '.' . $image->getClientOriginalExtension();
        $path = $this->disk->putFileAs($path, $image, $filename);

        Log::info(' Imagem enviada', [
            'user_id' => $userId,
            'path' => $path,
            'original_name' => $image->getClientOriginalName(),
            'mime' => $image->getMimeType(),
            'size_kb' => round($image->getSize() / 1024, 2),
        ]);

        return $path;
    }

    public function showImage(string $path)
    {
        if ($this->disk->exists($path)) {
            return $this->disk->url($path);
        }

        Log::warning(' Tentativa de acessar imagem inexistente', ['path' => $path]);
        return null;
    }

    public function downloadImage(string $path)
    {
        if ($this->disk->exists($path)) {
            Log::info('⬇ Download de imagem', ['path' => $path]);
            return $this->disk->download($path);
        }

        Log::warning('Tentativa de download de imagem inexistente', ['path' => $path]);
        abort(404, 'Imagem não encontrada');
    }

    public function deleteImage(string $path)
    {
        if ($this->disk->exists($path)) {
            $this->disk->delete($path);
            Log::info(' Imagem deletada', ['path' => $path]);
        } else {
            Log::warning(' Tentativa de deletar imagem inexistente', ['path' => $path]);
        }
    }

   public function cropImage(string $path, int $width, int $height)
{
    // Corrige caminhos relativos
    $path = str_replace(['storage/', 'public/'], '', $path);

    if ($this->disk->exists($path)) {
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $fullPath = storage_path('app/public/' . $path);

        // Lê, recorta e sobrescreve a imagem
        $manager->read($fullPath)
            ->cover($width, $height)
            ->save($fullPath);

        Log::info('Imagem recortada', [
            'path' => $path,
            'width' => $width,
            'height' => $height,
        ]);

        // Retorna o caminho correto da imagem (relativo)
        return $path;
    }

    Log::warning('Tentativa de recorte em imagem inexistente', ['path' => $path]);
    return null;
}
}
