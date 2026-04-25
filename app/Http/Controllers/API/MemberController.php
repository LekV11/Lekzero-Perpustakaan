<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all();
        return $this->sendResponse($members, 'Members retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'member_id' => 'required|string|max:50|unique:members,member_id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $member = Member::create($request->all());
        return $this->sendResponse($member, 'Member created successfully.', 201);
    }

    public function show(Member $member)
    {
        return $this->sendResponse($member, 'Member retrieved successfully.');
    }

    public function update(Request $request, Member $member)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'member_id' => 'sometimes|required|string|max:50|unique:members,member_id,'.$member->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $member->update($request->all());
        return $this->sendResponse($member, 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return $this->sendResponse([], 'Member deleted successfully.', 204);
    }
}
