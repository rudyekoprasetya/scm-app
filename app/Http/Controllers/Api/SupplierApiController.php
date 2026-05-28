<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view-suppliers');
        return SupplierResource::collection(Supplier::latest()->paginate(20));
    }

    public function show(Supplier $supplier): SupplierResource
    {
        $this->authorize('view-suppliers');
        return new SupplierResource($supplier);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create-suppliers');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $supplier = Supplier::create($data);

        return SupplierResource::make($supplier)->response()->setStatusCode(201);
    }

    public function update(Request $request, Supplier $supplier): SupplierResource
    {
        $this->authorize('edit-suppliers');
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $supplier->update($data);

        return new SupplierResource($supplier);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->authorize('delete-suppliers');
        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted.']);
    }
}
