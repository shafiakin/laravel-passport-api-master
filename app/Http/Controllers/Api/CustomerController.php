<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * List all customers.
     * 
     * @authenticated
     * @group Customers
     * 
     * @response {
     *   "message": "Record found",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "phone": "123-456-7890",
     *       "address": "123 Main St",
     *       "created_at": "2024-05-30T00:00:00.000000Z",
     *       "updated_at": "2024-05-30T00:00:00.000000Z"
     *     }
     *   ]
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $customers = Customer::all();
        return response()->json([
            'message' => 'Record found',
            'data' => $customers,
        ]);
    }

    /**
     * Show a single customer.
     * 
     * @authenticated
     * @group Customers
     * 
     * @urlParam id int required The ID of the customer. Example: 1
     * 
     * @response 200 {
     *   "message": "Customer's Record found",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "phone": "123-456-7890",
     *     "address": "123 Main St",
     *     "created_at": "2024-05-30T00:00:00.000000Z",
     *     "updated_at": "2024-05-30T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 404 {
     *   "message": "Customer not found"
     * }
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Create a new customer.
     * 
     * @authenticated
     * @group Customers
     * 
     * @bodyParam name string required The name of the customer. Example: John Doe
     * @bodyParam email string required The email of the customer. Example: john@example.com
     * @bodyParam phone string The phone number of the customer. Example: 123-456-7890
     * @bodyParam address string The address of the customer. Example: 123 Main St
     * 
     * @response 201 {
     *   "status": true,
     *   "message": "Customer created Successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "phone": "123-456-7890",
     *     "address": "123 Main St",
     *     "created_at": "2024-05-30T00:00:00.000000Z",
     *     "updated_at": "2024-05-30T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 400 {
     *   "name": ["The name field is required."],
     *   "email": ["The email field is required."]
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Update an existing customer.
     * 
     * @authenticated
     * @group Customers
     * 
     * @urlParam id int required The ID of the customer. Example: 1
     * @bodyParam name string The name of the customer. Example: John Doe
     * @bodyParam email string The email of the customer. Example: john@example.com
     * @bodyParam phone string The phone number of the customer. Example: 123-456-7890
     * @bodyParam address string The address of the customer. Example: 123 Main St
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Customer updated Successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "phone": "123-456-7890",
     *     "address": "123 Main St",
     *     "created_at": "2024-05-30T00:00:00.000000Z",
     *     "updated_at": "2024-05-30T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 404 {
     *   "message": "Customer not found"
     * }
     * 
     * @response 400 {
     *   "name": ["The name field is required."],
     *   "email": ["The email field is required."]
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Delete a customer.
     * 
     * @authenticated
     * @group Customers
     * 
     * @urlParam id int required The ID of the customer. Example: 1
     * 
     * @response 200 {
     *   "message": "Customer deleted successfully"
     * }
     * 
     * @response 404 {
     *   "message": "Customer not found"
     * }
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }


    /**
     * Get a customer with their orders.
     *
     * @authenticated
     * @group Customer Management
     * 
     * @urlParam id integer required The ID of the customer.
     * 
     * @response {
     *  "status": true,
     *  "message": "Customer and Orders retrieved successfully",
     *  "data": {
     *    "customer": {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com",
     *      "created_at": "2024-05-30T00:00:00.000000Z",
     *      "updated_at": "2024-05-30T00:00:00.000000Z"
     *    },
     *    "orders": [
     *      {
     *        "id": 1,
     *        "customer_id": 1,
     *        "product": "Product A",
     *        "quantity": 2,
     *        "created_at": "2024-05-30T00:00:00.000000Z",
     *        "updated_at": "2024-05-30T00:00:00.000000Z"
     *      },
     *      {
     *        "id": 2,
     *        "customer_id": 1,
     *        "product": "Product B",
     *        "quantity": 1,
     *        "created_at": "2024-05-30T00:00:00.000000Z",
     *        "updated_at": "2024-05-30T00:00:00.000000Z"
     *      }
     *    ]
     *  }
     * }
     * 
     * @response 404 {
     *  "message": "Customer not found"
     * }
     */
    public function getCustomerWithOrders($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $orders = $customer->orders()->get();

        return response()->json([
            'status' => true,
            'message' => 'Customer and Orders retrieved successfully',
            'data' => [
                'customer' => $customer,
                'orders' => $orders,
            ],
        ]);
    }

}


