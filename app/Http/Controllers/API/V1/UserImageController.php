<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\ImageService;
use App\Services\ImageService as ServicesImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserImageController extends Controller
{
    protected $imageService;

    public function __construct(ServicesImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Upload ou atualização da imagem do usuário
     */
    public function store(Request $request, User $user)
    {
        $this->authorize('manageImage', $user);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Apaga imagem antiga, se existir
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        // Faz upload com nome único e registra log
        $imagePath = $this->imageService->uploadImage($request->file('image'), 'users', $user->id);
        $user->update(['image' => $imagePath]);

        return response()->json([
            'success' => true,
            'message' => 'Imagem atualizada com sucesso.',
            'data' => [
                'image' => $this->imageService->showImage($imagePath),
            ]
        ], 200);
    }

    /**
     * Mostrar imagem do usuário
     */
    public function show(User $user)
    {
        $this->authorize('viewImage', $user);

        if (!$user->image) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui uma imagem.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagem do usuário.',
            'data' => [
                'image' => $this->imageService->showImage($user->image),
            ]
        ], 200);
    }

    /**
     * Download da imagem
     */
    public function download(User $user)
    {
        $this->authorize('viewImage', $user);

        if (!$user->image) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui uma imagem.',
            ], 404);
        }

        return $this->imageService->downloadImage($user->image);
    }
   public function crop(Request $request, User $user)
{
    $this->authorize('manageImage', $user);

    $request->validate([
        'width' => 'required|integer|min:50',
        'height' => 'required|integer|min:50',
    ]);

    if (!$user->image) {
        return response()->json([
            'success' => false,
            'message' => 'Usuário não possui imagem para recortar.',
        ], 404);
    }

    $croppedPath = $this->imageService->cropImage($user->image, $request->width, $request->height);

    if (!$croppedPath) {
        return response()->json([
            'success' => false,
            'message' => 'Falha ao recortar imagem.',
        ], 500);
    }

    // Atualiza o campo no banco com o caminho correto
    $user->update(['image' => $croppedPath]);

    return response()->json([
        'success' => true,
        'message' => 'Imagem recortada com sucesso.',
        'data' => [
            'image' => $this->imageService->showImage($croppedPath),
        ],
    ], 200);
}


    /**
     * Deletar imagem do usuário
     */
    public function destroy(User $user)
    {
        $this->authorize('manageImage', $user);

        if (!$user->image || !Storage::disk('public')->exists($user->image)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui uma imagem.',
            ], 404);
        }

        $this->imageService->deleteImage($user->image);
        $user->update(['image' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Imagem deletada com sucesso.',
        ], 200);
    }
}
