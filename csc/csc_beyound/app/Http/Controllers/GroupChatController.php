<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSent;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class GroupChatController extends Controller
{
    public function groupChatForm($subjectId)
    {
        $subject = DB::table('subjects')
        ->where('id', $subjectId)
        ->first();
    
        $messages = DB::table('chats')
        ->where('subject_id', $subjectId)
        ->join('users', 'users.id', '=', 'chats.sender_id') 
        ->select('chats.*', 'users.name as sender_name', 'users.id as sender_id') // Include sender_id
        ->orderBy('created_at', 'asc') 
        ->get();
    
    return view('groupchat', [
        'subject' => $subject,
        'messages' => $messages,
    ]);
    }

    public function sendMessage(Request $request, $subjectId)
    {
        $validatedData = $request->validate([
            'message' => 'required|string|max:255',
        ]);
    
        $subject = Subject::find($subjectId);
        if (!$subject) {
            return response()->json(['error' => 'Subject not found'], 404);
        }
        $messageText = $validatedData['message'];
    
        $chatMessage = new Chat;
        $chatMessage->sender_id = Auth::id(); 
        $chatMessage->subject_id = $subjectId;
        $chatMessage->message = $messageText;
        $chatMessage->save();
        $senderName = Auth::user()->name;
        
        event(new MessageSent($messageText, $subject, $senderName));
    
        return response()->json(['message' => 'Message sent successfully'], 200);
    }
    
}
