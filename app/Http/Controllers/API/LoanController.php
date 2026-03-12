<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        return response()->json(Loan::with(['member', 'book'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => 'required|string|in:borrowed,returned',
        ]);

        $loan = Loan::create($request->all());
        return response()->json($loan->load(['member', 'book']), 201);
    }

    public function show(Loan $loan)
    {
        return response()->json($loan->load(['member', 'book']));
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'member_id' => 'sometimes|required|exists:members,id',
            'book_id' => 'sometimes|required|exists:books,id',
            'loan_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => 'sometimes|required|string|in:borrowed,returned',
        ]);

        $loan->update($request->all());
        return response()->json($loan->load(['member', 'book']));
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return response()->json(null, 204);
    }
}
