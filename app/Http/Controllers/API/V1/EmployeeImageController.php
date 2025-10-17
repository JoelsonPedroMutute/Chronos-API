<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\EmployeeImageService;
use Illuminate\Http\Request;

class EmployeeImageController extends Controller
{
    protected $EmployeeImageService;
    public function __construct(EmployeeImageService $EmployeeImageService)
    {
        $this->EmployeeImageService = $EmployeeImageService;
    }
    public function store(Request $request, $employeeId)
    {
        $this->authorize('manageImage', Employee::class);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->EmployeeImageService->uploadImage($request->file('image'), 'employees', $employeeId);

            return response()->json([
                'success' => true,
                'message' => 'Imagem do funcionário atualizada com sucesso.',
                'data' => [
                    'image' => $this->EmployeeImageService->showImage($imagePath),
                ]
            ], 200);
        }
    }

    public function show($employeeId)
    {
        $this->authorize('viewImage', Employee::class);

        $imagePath = $this->EmployeeImageService->getEmployeeImagePath($employeeId);

        if ($imagePath) {
            return response()->json([
                'success' => true,
                'data' => [
                    'image' => $this->EmployeeImageService->showImage($imagePath),
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Imagem do funcionário não encontrada.',
        ], 404);
    }
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
            'message' => 'Usuário não possui imagem para recortar.',
        ], 404);
    }

    $croppedPath = $this->EmployeeImageService->cropImage($employee->image, $request->width, $request->height);

    if (!$croppedPath) {
        return response()->json([
            'success' => false,
            'message' => 'Falha ao recortar imagem.',
        ], 500);
    }
    }


    public function download($employeeId)
    {
        $this->authorize('viewImage', Employee::class);

        $imagePath = $this->EmployeeImageService->getEmployeeImagePath($employeeId);

        if ($imagePath) {
            return $this->EmployeeImageService->downloadImage($imagePath);
        }

        return response()->json([
            'success' => false,
            'message' => 'Imagem do funcionário não encontrada para download.',
        ], 404);
    }
    public function destroy($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorize('manageImage', $employee);


        $deleted = $this->EmployeeImageService->deleteImage($employeeId);

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
