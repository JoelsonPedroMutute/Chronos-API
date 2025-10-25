<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\ImageService;
use App\Services\ImageService as ServicesImageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;

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
public function store(Request $request, $userId)
{
     if(!Str::isUuid($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'ID de usuário inválido.',
        ], 400);
    }
    try {
        $user = User::findOrFail($userId); 
        $this->authorize('manageImage', $user);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        // Apaga imagem antiga
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        // Faz upload da nova imagem
        $imagePath = $this->imageService->uploadImage($request->file('image'), 'users', $user->id);
        $user->update(['image' => $imagePath]);

        return response()->json([
            'success' => true,
            'message' => 'Imagem atualizada com sucesso.',
            'data' => [
                'image' => $this->imageService->showImage($imagePath),
            ]
        ], 200);

    } catch (ModelNotFoundException $e) {
        //  Personalização do erro "User não encontrado"
        return response()->json([
            'success' => false,
            'message' => 'Usuário não encontrado.',
            'error' => $e->getModel(),
        ], 404);

    } catch (Exception $e) {
        // 🔴Captura qualquer outro erro inesperado
        return response()->json([
            'success' => false,
            'message' => 'Ocorreu um erro ao atualizar a imagem.',
            'error' => $e->getMessage(),
        ], 500);
    }
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
