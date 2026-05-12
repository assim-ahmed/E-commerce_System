<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Get all addresses for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = Address::where('user_id', $request->user()->id)->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
            'message' => 'Addresses retrieved successfully'
        ], 200);
    }

    /**
     * Create a new address
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // If this is the first address or is_default is true, make it default
        $isDefault = $request->is_default ?? false;
        $existingAddressesCount = Address::where('user_id', $request->user()->id)->count();

        if ($existingAddressesCount === 0) {
            $isDefault = true;
        }

        if ($isDefault) {
            // Remove default from other addresses
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => $request->user()->id,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'country' => $request->country,
            'phone' => $request->phone,
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address created successfully'
        ], 201);
    }

    /**
     * Update an address
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $address = Address::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Address not found',
                'errors' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle default address logic
        if ($request->is_default === true) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update($request->only([
            'address_line_1',
            'address_line_2',
            'city',
            'country',
            'phone',
            'is_default'
        ]));

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address updated successfully'
        ], 200);
    }

    /**
     * Delete an address
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = Address::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Address not found',
                'errors' => null
            ], 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If we deleted the default address, set another one as default
        if ($wasDefault) {
            $newDefault = Address::where('user_id', $request->user()->id)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Address deleted successfully'
        ], 200);
    }
}