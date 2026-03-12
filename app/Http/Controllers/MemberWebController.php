<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberWebController extends Controller
{
    public function index()
    {
        $members = Member::paginate(15);
        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => 'required|string|max:50|unique:members,member_id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Member::create($data);
        return redirect()->route('members.index')->with('success', 'Anggota ditambahkan');
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => 'required|string|max:50|unique:members,member_id,'.$member->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $member->update($data);
        return redirect()->route('members.index')->with('success', 'Anggota diperbarui');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Anggota dihapus');
    }

    public function show(Member $member)
    {
        return redirect()->route('members.index');
    }
}
