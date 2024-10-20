<?php
namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupMemberController extends Controller
{
    public function addMember(Request $request, $groupId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
        ]);

        GroupMember::create([
            'group_id' => $groupId,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'joined_at' => now(),
        ]);

        return response()->json(['message' => 'Member added successfully.'], 201);
    }

    public function removeMember($groupId, $userId)
    {
        GroupMember::where('group_id', $groupId)->where('user_id', $userId)->delete();
        return response()->json(['message' => 'Member removed successfully.']);
    }

    public function updateMember(Request $request, $groupId, $userId)
    {
        $request->validate([
            'role' => 'sometimes|required|string',
        ]);

        $member = GroupMember::where('group_id', $groupId)->where('user_id', $userId)->first();

        if (!$member) {
            return response()->json(['message' => 'Member not found.'], 404);
        }

        if ($request->has('role')) {
            $member->role = $request->role;
        }

        $member->save();

        return response()->json(['message' => 'Member updated successfully.', 'member' => $member]);
    }

    public function listMembers($groupId)
    {
        $members = GroupMember::where('group_id', $groupId)->with('user')->get();
        return response()->json($members);
    }
}