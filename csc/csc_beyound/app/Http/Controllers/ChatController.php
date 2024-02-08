<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Events\MessageSent;
class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = auth()->user();
        $receiverId = $request->receiver_id;
        $messageText = $request->message;
    
        $chatMessage = Chat::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $messageText,
        ]);
    
        broadcast(new MessageSent($chatMessage))->toOthers();
    
        return response()->json(['message' => 'Message sent successfully']);
    }
    
    

public function setReceiver(Request $request)
{
    session(['receiver_id' => $request->receiver_id]);
    return response()->json(['message' => 'Receiver ID set successfully']);
}

public function fetchUsers(Request $request)
{
    $loggedInUser = auth()->user();
    
    if ($loggedInUser->role === 'teacher') {
        $users = User::where('role', 'student')->where('id', '!=', $loggedInUser->id)->get();
    } else {
        $users = User::where('role', 'teacher')->where('id', '!=', $loggedInUser->id)->get();
    }
    
    return response()->json(['users' => $users]);
}


public function fetchChatHistory($receiverId)
{
    try {
        $userId = auth()->id();

        $messages = Chat::where(function($query) use ($userId, $receiverId) {
                            $query->where('sender_id', $userId)->where('receiver_id', $receiverId);
                        })
                        ->orWhere(function($query) use ($userId, $receiverId) {
                            $query->where('sender_id', $receiverId)->where('receiver_id', $userId);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        return response()->json(['messages' => $messages]);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json(['error' => 'An error occurred while fetching chat history.']);
    }
}



}
