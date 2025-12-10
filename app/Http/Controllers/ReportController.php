<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Report::query();
        if ($user->role == 'technician') {
            $query->where('user_id', $user->id);
        } elseif ($user->role == 'reception' || $user->role == 'supply') {
            $reports = Report::all();
        } elseif ($user->role == 'reception' || $user->role == 'supply') {
            $reports = Report::where('status', 'pending')->get();
        } elseif ($user->role == 'ceo') {
            $reports = Report::all();
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('part_name', 'like', "%$request->search%")
                    ->orWhere('request_number', 'like', "%$request->search%")
                    ->orWhere('serial_number', 'like', "%$request->search%")
                    ->orWhere('device_model', 'like', "%$request->search%")
                    ->orWhereRelation('user', 'name', 'like', "%$request->search%");
            });
        }

        $reports = $query->orderBy('id', 'desc')->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        if (Auth::user()->role != 'technician') {
            return redirect()->route('reports.index')->with('error', 'شما اجازه ایجاد گزارش را ندارید.');
        }

        return view('reports.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'technician') {
            return redirect()->route('reports.index')->with('error', 'شما اجازه ایجاد گزارش را ندارید.');
        }
        $request->validate([
            'part_name' => 'required|string|max:255',
            'request_date' => 'required|date',
            'request_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'used_parts_list' => 'nullable|array',
            'device_model' => 'required|string|max:50',
            'activity_report' => 'required|string',
            'workers_count' => 'required|integer|min:1',
            'hours_per_worker' => 'required|numeric|min:0.5',
            'end_date' => 'required|date',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'part_name' => $request->part_name,
            'request_date' => $request->request_date,
            'request_number' => $request->request_number,
            'serial_number' => $request->serial_number,
            'device_model' => $request->device_model,
            'issue_description' => $request->issue_description,
            'activity_report' => $request->activity_report,
            'used_parts_list' => json_encode($request->used_parts_list),
            'workers_count' => $request->workers_count,
            'hours_per_worker' => $request->hours_per_worker,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);

        return redirect()->route('reports.index');
    }

    public function show(Report $report)
    {
        if (Auth::user()->id == $report->user_id || Auth::user()->role == 'ceo' || Auth::user()->role == 'reception' || Auth::user()->role == 'supply') {
            return view('reports.show', compact('report'));
        }

        return redirect()->route('reports.index')->with('error', 'شما اجازه دسترسی به این گزارش را ندارید.');
    }

    public function edit(Report $report)
    {
        if (Auth::user()->id == $report->user_id && Auth::user()->role == 'technician') {
            return view('reports.edit', compact('report'));
        }

        return redirect()->route('reports.index')->with('error', 'شما اجازه ویرایش این گزارش را ندارید.');
    }

    public function update(Request $request, Report $report)
    {
        if (Auth::user()->id == $report->user_id && Auth::user()->role == 'technician') {
            $request->validate([
                'part_name' => 'required|string|max:255',
                'request_date' => 'required|date',
                'serial_number' => 'required|string|max:50',
                'device_model' => 'required|string|max:50',
                'issue_description' => 'required|string',
                'activity_report' => 'required|string',
            ]);

            $report->update($request->all());
            return redirect()->route('reports.index');
        }

        return redirect()->route('reports.index')->with('error', 'شما اجازه ویرایش این گزارش را ندارید.');
    }

    public function destroy(Report $report)
    {
        if (Auth::user()->id == $report->user_id && Auth::user()->role == 'technician') {
            $report->delete();
            return redirect()->route('reports.index');
        }

        return redirect()->route('reports.index')->with('error', 'شما اجازه حذف این گزارش را ندارید.');
    }
    public function approve(Report $report)
    {
        $role = auth()->user()->role;

        if ($role == 'reception')       $report->request_approval = 1;
        elseif ($role == 'supply')      $report->supply_approval = 1;
        elseif ($role == 'ceo')         $report->ceo_approval = 1;
        else return back()->with('error', 'اجازه تایید ندارید.');

        $report->save();

        $votes = [
            $report->request_approval,
            $report->supply_approval,
            $report->ceo_approval
        ];

        if (!in_array(null, $votes) && array_sum($votes) === 3) {
            $report->status = 'approved';
            $report->save();
        }

        if (in_array(null, $votes) || array_sum($votes) < 3) {
            $report->status = 'new';
            $report->save();
        }

        return back()->with('success', 'رای تایید شما ثبت شد');
    }


    public function reject(Report $report)
    {
        $role = auth()->user()->role;

        if ($role == 'reception') {
            $report->request_approval = 0;
        } elseif ($role == 'supply') {
            $report->supply_approval = 0;
        } elseif ($role == 'ceo') {
            $report->ceo_approval = 0;
        } else {
            return back()->with('error', 'اجازه رد گزارش ندارید.');
        }

        $report->save();

        $votes = [$report->request_approval, $report->supply_approval, $report->ceo_approval];

        if (!in_array(null, $votes, true) && array_sum($votes) == 0) {
            $report->status = 'rejected';
            $report->save();
        }

        return back()->with('success', 'رأی رد شما ثبت شد.');
    }
}
