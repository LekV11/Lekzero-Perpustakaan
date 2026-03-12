<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Book;
use Illuminate\Http\Request;

class LoanWebController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['member', 'book'])->paginate(15);
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $members = Member::all();
        $books = Book::all();
        return view('loans.create', compact('members', 'books'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => 'required|string|in:borrowed,returned',
        ]);

        Loan::create($data);
        return redirect()->route('loans.index')->with('success', 'Transaksi disimpan');
    }

    public function edit(Loan $loan)
    {
        $members = Member::all();
        $books = Book::all();
        return view('loans.edit', compact('loan', 'members', 'books'));
    }

    public function update(Request $request, Loan $loan)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => 'required|string|in:borrowed,returned',
        ]);

        $loan->update($data);
        return redirect()->route('loans.index')->with('success', 'Transaksi diperbarui');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return redirect()->route('loans.index')->with('success', 'Transaksi dihapus');
    }

    public function show(Loan $loan)
    {
        return redirect()->route('loans.index');
    }
}
