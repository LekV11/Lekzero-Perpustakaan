<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['member', 'book'])->get();
        return $this->sendResponse($loans, 'Loans retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => 'required|string|in:borrowed,returned',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $loan = Loan::create($request->all());
        return $this->sendResponse($loan->load(['member', 'book']), 'Loan created successfully.', 201);
    }

    public function show(Loan $loan)
    {
        return $this->sendResponse($loan->load(['member', 'book']), 'Loan retrieved successfully.');
    }

    public function update(Request $request, Loan $loan)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'member_id' => 'sometimes|required|exists:members,id',
            'book_id' => 'sometimes|required|exists:books,id',
            'loan_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => 'sometimes|required|string|in:borrowed,returned',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $loan->update($request->all());
        return $this->sendResponse($loan->load(['member', 'book']), 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return $this->sendResponse([], 'Loan deleted successfully.', 204);
    }
}
