<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     * 
     * @group Orders
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    /**
     * Store a newly created order in storage.
     * 
     * @group Orders
     * 
     * @bodyParam customer_id int required The ID of the customer. Example: 1
     * @bodyParam order_date date required The date of the order. Example: 2024-05-30
     * @bodyParam status string required The status of the order. Example: completed
     * @bodyParam total numeric required The total amount of the order. Example: 100.00
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'total' => 'required|numeric',
        ]);

        $order = Order::create($validatedData);
        return response()->json($order, 201);
    }

    /**
     * Display the specified order.
     * 
     * @group Orders
     * 
     * @urlParam id int required The ID of the order. Example: 1
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified order in storage.
     * 
     * @group Orders
     * 
     * @urlParam id int required The ID of the order. Example: 1
     * @bodyParam customer_id int required The ID of the customer. Example: 1
     * @bodyParam order_date date required The date of the order. Example: 2024-05-30
     * @bodyParam status string required The status of the order. Example: completed
     * @bodyParam total numeric required The total amount of the order. Example: 150.00
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'total' => 'required|numeric',
        ]);

        $order->update($validatedData);
        return response()->json($order);
    }

    /**
     * Remove the specified order from storage.
     * 
     * @group Orders
     * 
     * @urlParam id int required The ID of the order. Example: 1
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}

