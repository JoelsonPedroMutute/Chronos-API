<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\EmployeeImageService;
use Illuminate\Http\Request;

class EmployeeImageController extends Controller
{
    protected $employeeImageService;

    public function __construct(EmployeeImageService $employeeImageService)
    {
        $this->employeeImageService = $employeeImageService;
    }

    /**
     * Upload ou atualização da imagem de um funcionário.
     */
    public function store(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // Autoriza a ação
        $this->authorize('manageImage', $employee);

        // Bloqueia upload se o funcionário estiver vinculado a um usuário
        if ($employee->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível fazer upload de imagem para funcionários vinculados a usuários. A imagem deve ser gerenciada pelo perfil do usuário.',
            ], 403);
        }

        // Validação da imagem
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload e atualização
        if ($request->hasFile('image')) {
            $imagePath = $this->employeeImageService->uploadImage(
                $request->file('image'),
                'employees',
                $employeeId
            );

            if ($imagePath) {
                $employee->update(['image' => $imagePath]);

                return response()->json([
                    'success' => true,
                    'message' => 'Imagem do funcionário atualizada com sucesso.',
                    'data' => [
                        'image' => $this->employeeImageService->showImage($imagePath),
                    ]
                ], 201);
            }
        }

        // Caso o arquivo não tenha sido enviado ou algo deu errado
        return response()->json([
            'success' => false,
            'message' => 'Falha ao fazer upload da imagem do funcionário.',
        ], 500);
    }

    /**
     * Mostra a imagem de um funcionário.
     */
    public function show($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorize('viewImage', $employee);

        $imagePath = $this->employeeImageService->getEmployeeImagePath($employeeId);

        if ($imagePath) {
            return response()->json([
                'success' => true,
                'data' => [
                    'image' => $this->employeeImageService->showImage($imagePath),
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Imagem do funcionário não encontrada.',
        ], 404);
    }

    /**
     * Recorta a imagem de um funcionário.
     */
    public function cropImage(Request $request, Employee $employee)
    {
        $this->authorize('manageImage', $employee);

        $request->validate([
            'width' => 'required|integer|min:50',
            'height' => 'required|integer|min:50',
        ]);

        if (!$employee->image) {
            return response()->json([
                'success' => false,
                'message' => 'Funcionário não possui imagem para recortar.',
            ], 404);
        }

        $croppedPath = $this->employeeImageService->cropImage(
            $employee->image,
            $request->width,
            $request->height
        );

        if (!$croppedPath) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao recortar imagem.',
            ], 500);
        }

        $employee->update(['image' => $croppedPath]);

        return response()->json([
            'success' => true,
            'message' => 'Imagem recortada com sucesso.',
            'data' => [
                'image' => $this->employeeImageService->showImage($croppedPath),
            ]
        ], 200);
    }

    /**
     * Download da imagem.
     */
    public function download($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorize('viewImage', $employee);

        $imagePath = $this->employeeImageService->getEmployeeImagePath($employeeId);

        if ($imagePath) {
            return $this->employeeImageService->downloadImage($imagePath);
        }

        return response()->json([
            'success' => false,
            'message' => 'Imagem do funcionário não encontrada para download.',
        ], 404);
    }

    /**
     * Deleta a imagem de um funcionário.
     */
    public function destroy($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorize('manageImage', $employee);

        $deleted = $this->employeeImageService->deleteImage($employeeId);

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Imagem do funcionário deletada com sucesso.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Imagem do funcionário não encontrada para deleção.',
        ], 404);
    }
     
}
