<?php

namespace App\Http\Controllers;

use App\Models\PartOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'technician') {
            $partOrders = PartOrder::where('user_id', $user->id)->get();
        } elseif ($user->role == 'reception' || $user->role == 'supply') {
            $partOrders = PartOrder::where('status', 'pending')->get();
        } elseif ($user->role == 'ceo') {
            $partOrders = PartOrder::all();
        }

        return view('partorders.index', compact('partOrders'));
    }

    public function create()
    {
        if (Auth::user()->role == 'technician') {
            return view('partorders.create');
        }

        return redirect()->route('partorders.index')->with('error', 'شما اجازه ایجاد سفارش ندارید.');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'technician') {
            return redirect()->route('partorders.index')->with('error', 'شما اجازه ایجاد سفارش ندارید.');
        }
        $request->validate([
            'equipment_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'order_number' => 'required|string|max:50',
            'part_name' => 'required|string|max:255',
            'specifications' => 'required|string',
            'package' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        PartOrder::create([
            'user_id' => auth()->id(),
            'equipment_name' => $request->equipment_name,
            'order_date' => $request->order_date,
            'order_number' => $request->order_number,
            'part_name' => $request->part_name,
            'specifications' => $request->specifications,
            'package' => $request->package,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('partorders.index');
    }

    public function show(PartOrder $partOrder)
    {
        if (Auth::user()->role == 'technician' || Auth::user()->role == 'ceo' || Auth::user()->role == 'reception' || Auth::user()->role == 'supply') {
            return view('partorders.show', compact('partOrder'));
        }

        return redirect()->route('partorders.index')->with('error', 'شما اجازه دسترسی به این سفارش را ندارید.');
    }

    public function edit(PartOrder $partOrder)
    {
        if (Auth::user()->id == $partOrder->user_id && Auth::user()->role == 'technician') {
            return view('partorders.edit', compact('partOrder'));
        }

        return redirect()->route('partorders.index')->with('error', 'شما اجازه ویرایش این سفارش را ندارید.');
    }

    public function update(Request $request, PartOrder $partOrder)
    {
        if (Auth::user()->id == $partOrder->user_id && Auth::user()->role == 'technician') {
            $request->validate([
                'equipment_name' => 'required|string|max:255',
                'order_date' => 'required|date',
                'order_number' => 'required|string|max:50',
                'part_name' => 'required|string|max:255',
                'specifications' => 'required|string',
                'package' => 'required|string',
                'quantity' => 'required|integer',
            ]);

            // $partOrder->update([
            //     'equipment_name' => $request->equipment_name,
            //     'order_date' => $request->order_date,
            //     'order_number' => $request->order_number,
            //     'part_name' => $request->part_name,
            //     'specifications' => $request->specifications,
            //     'package' => $request->package,
            //     'quantity' => $request->quantity,
            //     'description' => $request->description,
            // ]);
            $partOrder->update($request->all());
            return redirect()->route('partorders.index');
        }
        return redirect()->route('partorders.index')->with('error', 'شما اجازه ویرایش این سفارش را ندارید.');
    }

    public function destroy(PartOrder $partOrder)
    {
        if (Auth::user()->id == $partOrder->user_id && Auth::user()->role == 'technician') {
            $partOrder->delete();
            return redirect()->route('partorders.index');
        }

        return redirect()->route('partorders.index')->with('error', 'شما اجازه حذف این سفارش را ندارید.');
    }
    public function approve(PartOrder $partOrder)
    {
        if (Auth::user()->role == 'ceo' || Auth::user()->role == 'reception' || Auth::user()->role == 'supply') {
            $partOrder->status = 'approved';
            $partOrder->save();

            return redirect()->route('partorders.index')->with('success', 'سفارش قطعه تایید شد');
        }

        return redirect()->route('partorders.index')->with('error', 'شما اجازه دسترسی به این سفارش را ندارید.');
    }

    public function reject(PartOrder $partOrder)
    {
        if (Auth::user()->role == 'ceo' || Auth::user()->role == 'reception' || Auth::user()->role == 'supply') {
            $partOrder->status = 'rejected';
            $partOrder->save();

            return redirect()->route('partorders.index')->with('success', 'سفارش قطعه رد شد');
        }

        return redirect()->route('partorders.index')->with('error', 'شما اجازه دسترسی به این سفارش را ندارید.');
    }
}
