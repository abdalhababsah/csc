<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Events\PrivateMessageSent;
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
    
        $receiver = User::find($receiverId); 
        // dd($receiver);
        event(new PrivateMessageSent($receiver, $messageText));
    
        return response()->json(['message' => 'Message sent successfully']);
    }
    
    
    

public function setReceiver(Request $request)
{
    session(['receiver_id' => $request->receiver_id]);
    return response()->json(['message' => 'Receiver ID set successfully']);
}

public function chatView($subjectId)
{
    return view('admin.chat.chatview', compact('subjectId'));
}

public function fetchUsers($subjectId)
{
    $loggedInUserId = auth()->id();
    $loggedInUserRole = auth()->user()->role; 

    $users = collect();

    if ($loggedInUserRole == 'student') {
        // $students = User::whereHas('subjects', function ($query) use ($subjectId) {
        //                 $query->where('subject_id', $subjectId);
        //             })
        //             ->where('role', '=', 'student')
        // ->where('id', '!=', $loggedInUserId)

                    // ->get();
                    $students = User::whereHas('subjects', function ($query) use ($subjectId) {
                        $query->where('subject_id', $subjectId);
                    })
                    ->where('role', '=', 'student')
                    ->where('id', '!=', $loggedInUserId)
                    ->get();

        $teachers = User::where('role', '=', 'teacher')->get(); 

        $users = $students->merge($teachers);
    } else if ($loggedInUserRole == 'teacher') {
        $students = User::whereHas('subjects', function ($query) use ($subjectId) {
                        $query->where('subject_id', $subjectId);
                    })
                    ->where('role', '=', 'student')
                    ->get();

        $teachers = User::where('role', '=', 'teacher')
                        ->where('id', '!=', $loggedInUserId)
                        ->get();

        $users = $students->merge($teachers);
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
