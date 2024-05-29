<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // List all customers (GET)
    public function index()
    {
        $customers = Customer::all();
        // return response()->json($customers);
        return response()->json([
            'message' => 'Record found',
            'data' => $customers,
        ]);
    }

    // Show a single customer (GET)
    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json([
            "message" => "Customer's Record found",
            "data" => $customer,
        ]);
    }

    // Create a new customer (POST)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $customer = Customer::create($request->all());
        return response()->json([
            "status" => true,
            "message" => "Customer created Successfully",
            "data" => $customer,
        ], 201);
    }

    // Update an existing customer (PUT)
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $customer->update($request->all());
        return response()->json([
            "status" => true,
            "message" => "Customer updated Successfully",
            "data" => $customer,
        ]);
    }

    // Delete a customer (DELETE)
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}

