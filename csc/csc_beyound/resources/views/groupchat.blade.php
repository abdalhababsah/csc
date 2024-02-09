@extends('dash_layouts.master')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.2/echo.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<link rel="stylesheet" href="{{asset('css/groupchat.css')}}">

<div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row container d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card card-bordered">
                    <div class="card-header">
                        <h4 class="card-title"><strong>{{ $subject->sub_name }} Chat</strong></h4>
                        <a class="btn btn-xs btn-secondary" href="#" data-abc="true">Let's Chat App</a>
                    </div>

                    <div class="ps-container ps-theme-default ps-active-y" id="chat_area" style="overflow-y: scroll !important; height:400px !important;">
                    </div>

                    <div class="publisher bt-1 border-light">
                        <img class="avatar avatar-xs" src="https://img.icons8.com/color/36/000000/administrator-male.png" alt="...">
                        <input class="publisher-input" id="message" type="text" placeholder="Write something">
                        <span class="publisher-btn file-group">
                          <i class="fa fa-paperclip file-browser"></i>
                          <input type="file">
                        </span>
                        <button id="send" class="publisher-btn text-info" data-abc="true"><i class="fa fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var currentUserId = "{{ Auth::user()->id }}"; 
    
        function appendMessage(message, senderName, senderId, isCurrentUser = null) {
            console.log(message, senderName, senderId, isCurrentUser)
            if(isCurrentUser === null) {
                isCurrentUser = senderId === currentUserId;
            }
            var alignmentClass = isCurrentUser ? 'media-chat-reverse' : '';
            var displayName = isCurrentUser ? '' : '<strong>' + senderName + ': </strong>';
            var messageHtml = `<div class="media media-chat ${alignmentClass}">
                                  <div class="media-body">
                                    <p>${displayName}${message}</p>
                                  </div>
                               </div>`;
            $("#chat_area").append(messageHtml);
            $("#chat_area").scrollTop($("#chat_area")[0].scrollHeight); 
        }
    
        // Function to render the initial chat history
        function renderChatHistory(messages) {
            messages.forEach(function(message) {
                appendMessage(message.message, message.sender_name, message.sender_id);
            });
        }
    
        // Initial rendering of chat history (assuming messages are passed as a JSON object)
        @if(isset($messages))
        var messages = @json($messages);
        renderChatHistory(messages);
        @endif
    
        // Handling the sending of a new message
        $("#send").click(function(e){
            e.preventDefault();
            let message = $("#message").val();
            if (message.trim() != '') {
                $.ajax({
                    url: "/groupchat/{{$subject->id}}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "message": message,
                    },
                    success: function(response) {
                        console.log(message, 'You', currentUserId, true);
                        $("#message").val('');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + status + " " + error);
                    }
                });
            }
        });
    
        Pusher.logToConsole = true;
        var pusher = new Pusher('608166aa2dcfb3345187', {
          cluster: 'ap2',
          encrypted: true
        });
    
        var channel = pusher.subscribe('public-chat-channel');
        channel.bind('sendMessage', function(data) {
            appendMessage(data.message, data.senderName, data.senderId);
        });
    });
</script>
    
    






@endsection


