<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class EmployeeImageService
{
    protected FilesystemAdapter $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Faz upload da imagem do funcionário.
     */
    public function uploadImage(UploadedFile $image, string $folder, string $employeeId): string
    {
        $directory = "{$folder}/{$employeeId}";
        $filename = 'profile_' . time() . '.' . $image->getClientOriginalExtension();

        $path = $image->storeAs($directory, $filename, 'public');

        Log::info("📤 Imagem do funcionário {$employeeId} carregada em: {$path}");

        return $path;
    }

    /**
     * Retorna a URL pública da imagem.
     */
    public function showImage(string $path): string
    {
        return $this->disk->url($path);
    }

    /**
     * Retorna o caminho completo da imagem atual de um funcionário.
     */
    public function getEmployeeImagePath(string $employeeId): ?string
    {
        $directory = "employees/{$employeeId}";
        $files = $this->disk->files($directory);

        return count($files) > 0 ? $files[0] : null;
    }

    /**
     * Faz o download da imagem.
     */
    public function downloadImage(string $path): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->disk->download($path);
    }

    /**
     * Deleta a imagem atual de um funcionário.
     */
    public function deleteImage(string $employeeId): bool
    {
        $path = $this->getEmployeeImagePath($employeeId);

        if ($path && $this->disk->exists($path)) {
            $this->disk->delete($path);
            Log::info(" Imagem do funcionário {$employeeId} deletada: {$path}");
            return true;
        }

        return false;
    }

    /**
     * Substitui a imagem existente por uma nova.
     */
    public function updateImage(UploadedFile $newImage, string $employeeId): string
    {
        $this->deleteImage($employeeId);
        return $this->uploadImage($newImage, 'employees', $employeeId);
    }

    /**
     * Recorta e redimensiona a imagem usando Intervention Image.
     */
  public function cropImage(string $imagePath, int $width = 300, int $height = 300): ?string
{
    if (!$this->disk->exists($imagePath)) {
        Log::error("Imagem não encontrada em: {$imagePath}");
        return null;
    }

    $employeeId = (int) basename(dirname($imagePath));

    $directory = "employees/{$employeeId}";
    $filename = 'profile_cropped_' . time() . '.jpeg';
    $path = "{$directory}/{$filename}";

    $manager = new ImageManager(new Driver());
    $image = $manager->read($this->disk->path($imagePath));

    // Recorta e redimensiona
    $image = $image->cover($width, $height);

    $this->disk->put($path, (string) $image->encode());

    Log::info("Imagem recortada e salva em: {$path}");

    return $path;
}
}
