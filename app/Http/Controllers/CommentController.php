<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        if (Auth::user()->role != 'ceo') {
            return redirect()->route('reports.index')->with('error', 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        $comments = Comment::all();
        return view('comments.index', compact('comments'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'ceo') {
            return redirect()->route('reports.index')->with('error', 'شما اجازه ثبت کامنت ندارید.');
        }
        $request->validate([
            'comment' => 'required|string',
            'report_id' => 'required|exists:reports,id',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'report_id' => $request->report_id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('reports.index')->with('success', 'کامنت با موفقیت ارسال شد.');
    }

    public function show(Report $report)
    {
        if (Auth::user()->id == $report->user_id || Auth::user()->role == 'ceo') {
            $comments = $report->comments;
            return view('comments.show', compact('comments', 'report'));
        }

        return redirect()->route('reports.index')->with('error', 'شما اجازه دسترسی به کامنت‌های این گزارش را ندارید.');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::user()->role == 'ceo') {
            $comment->delete();
            return redirect()->route('comments.index')->with('success', 'کامنت با موفقیت حذف شد.');
        }
        return redirect()->route('comments.index')->with('error', 'شما اجازه حذف این کامنت را ندارید.');
    }
}
