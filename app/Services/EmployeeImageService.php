<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;

class EmployeeImageService
{ protected FilesystemAdapter $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }
    public function uploadImage(UploadedFile $image, string $folder, int $employeeId): string
    {
        $filename = 'employee_' . $employeeId . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs($folder, $filename, 'public');

        Log::info("Imagem do funcionário {$employeeId} carregada em: {$path}");

        return $path;
    }
    public function showImage(string $path): string
    {
        return $this->disk->url($path);
    }
    public function getEmployeeImagePath(int $employeeId): ?string
    {
        $path = "employees/{$employeeId}/profile.jpg";

        if ($this->disk->exists($path)) {
            return $path;
        }

        return null;
    }
    public function downloadImage(string $path): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->disk->download($path);
    }
    public function deleteImage(int $employeeId): bool
    {
        $path = $this->getEmployeeImagePath($employeeId);

        if ($path) {
            return $this->disk->delete($path);
        }

        return false;
    }
    public function updateImage(UploadedFile $newImage, int $employeeId): string
    {
        $this->deleteImage($employeeId);

        return $this->uploadImage($newImage, 'employees', $employeeId);
    }
    public function cropImage(UploadedFile $croppedImage, int $employeeId): string
    {
        $this->deleteImage($employeeId);

        return $this->uploadImage($croppedImage, 'employees', $employeeId);
    }
    public function deleteEmployeeImage(int $employeeId): bool
    {
        return $this->deleteImage($employeeId);
    }
    
}