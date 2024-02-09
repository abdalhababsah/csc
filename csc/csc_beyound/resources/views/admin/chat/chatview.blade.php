@extends('dash_layouts.master')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.2/echo.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">

    <div class="messaging">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="inbox_chat scroll">


                    {{-- Users will be dynamically appended here --}}


                </div>
            </div>
            <div class="mesgs">
                <div class="msg_history">


<div class="incoming_msg"><div class="incoming_msg_img"><img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"></div><div class="received_msg"><div class="received_withd_msg">
<p>Test which is a new approach to have allsolutions</p></div></div>


                    </div>


                    <div class="outgoing_msg"><div class="sent_msg"><p>Test which is a new approach to have all solutions</p></div></div>

                    <!-- Message history will be displayed here -->
                </div>
                <div class="type_msg">
                    <div class="input_msg_write">
                        <input type="text" class="write_msg" id="message" placeholder="Type a message" />
                        <button id="send" class="msg_send_btn" type="button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var currentUserId = "{{ Auth::user()->id }}"; 
            var selectedReceiverId; // Global variable to store the selected receiver's ID
        
            function appendMessage(message, senderName, senderId, isCurrentUser = null) {
                if(isCurrentUser === null) {
                    isCurrentUser = senderId === currentUserId;
                }
                var messageHtml;
                if(isCurrentUser) {
                    // Outgoing message format
                    messageHtml = `<div class="outgoing_msg">
                                      <div class="sent_msg">
                                        <p>${message}</p>
                                      </div>
                                   </div>`;
                } else {
                    // Incoming message format
                    messageHtml = `<div class="incoming_msg">
                                      <div class="incoming_msg_img">
                                        <img src="https://ptetutorials.com/images/user-profile.png" alt="${senderName}">
                                      </div>
                                      <div class="received_msg">
                                        <div class="received_withd_msg">
                                          <p><strong>${senderName}: </strong>${message}</p>
                                        </div>
                                      </div>
                                   </div>`;
                }
                $(".msg_history").prepend(messageHtml);
                $(".msg_history").scrollTop($(".msg_history")[0].scrollHeight);
            }
        
            // Function to fetch and display chat history
            function fetchChatHistory(receiverId) {
                $.ajax({
                    url: `/fetch-private-chat-history/${receiverId}`,
                    type: "GET",
                    success: function(response) {
                        $('.msg_history').empty(); // Clear the chat area before displaying new messages
                        response.messages.forEach(function(message) {
                            appendMessage(message.message, message.sender_name, message.sender_id, message.sender_id == currentUserId);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching chat history: " + status + " " + error);
                    }
                });
            }
        
            // Click event for user selection
            $(document).on('click', '.chat_list', function(){
                selectedReceiverId = $(this).data('receiver-id');
                $('.chat_list').removeClass('active_chat');
                $(this).addClass('active_chat');
                fetchChatHistory(selectedReceiverId); // Fetch and display the chat history with the selected user
            });
        
            // Sending a message
            $("#send").click(function(e){
                e.preventDefault();
                let message = $("#message").val();
                if (message.trim() != '' && selectedReceiverId) {
                    $.ajax({
                        url: "/send-private-message", 
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "message": message,
                            "receiver_id": selectedReceiverId,
                        },
                        success: function(response) {
                            appendMessage(message, 'You', currentUserId, true);
                            $("#message").val('');
                            fetchChatHistory(selectedReceiverId); // Optionally, refresh chat history after sending a message
                        },
                        error: function(xhr, status, error) {
                            console.error("Error: " + status + " " + error);
                        }
                    });
                } else {
                    alert('Please select a user to chat with.');
                }
            });
        
            // Fetching users for chat list
            let userid = '{{Auth::user()->id}}';
            $.get('/fetch-private-users/'+userid, function (data) {
                $('.inbox_chat').empty();
                data.users.forEach(function (user) {
                    var chatList = `
                        <div class="chat_list" data-receiver-id="${user.id}">
                            <div class="chat_people">
                                <div class="chat_img">
                                    <img src="https://ptetutorials.com/images/user-profile.png" alt="user">
                                </div>
                                <div class="chat_ib">
                                    <h5>${user.name}</h5>
                                </div>
                            </div>
                        </div>`;
                    $('.inbox_chat').append(chatList);
                });
            }).fail(function (xhr, status, error) {
                console.error('Error fetching users:', error);
            });
        
            // Real-time messaging setup (assumes Pusher is correctly configured)
            Pusher.logToConsole = true;
            var pusher = new Pusher('608166aa2dcfb3345187', {
              cluster: 'ap2',
              encrypted: true
            });
        
            var channel = pusher.subscribe('chat{{Auth::user()->id}}');
            channel.bind('PrivateMessageSent', function(data) {
                console.log(data);
                if(data.senderId == selectedReceiverId || data.senderId == currentUserId) {
                    appendMessage(data.message, data.senderName, data.senderId, data.senderId == currentUserId);
                }
            });
        });

        
        </script>

@endsection



 