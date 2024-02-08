
@extends('dash_layouts.master')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.2/echo.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.2/echo.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<link rel="stylesheet" href="{{asset('css/chat.css')}}">

<div class="messaging">
    <div class="inbox_msg">
        <div class="inbox_people">
            <div class="inbox_chat scroll">
                {{-- Users will be dynamically appended here --}}
            </div>
        </div>
        <div class="mesgs">
            <div class="msg_history">
                <div class="incoming_msg">
                    <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                    <div class="received_msg">
                      <div class="received_withd_msg">
                        <p>Test which is a new approach to have all
                          solutions</p>
                        <span class="time_date"> 11:01 AM    |    June 9</span></div>
                    </div>
                  </div>
                  <div class="outgoing_msg">
                    <div class="sent_msg">
                      <p>Test which is a new approach to have all
                        solutions</p>
                      <span class="time_date"> 11:01 AM    |    June 9</span> </div>
                  </div>
              
                <!-- Message history will be displayed here -->
            </div>
            <div class="type_msg">
                <div class="input_msg_write">
                    <input type="text" class="write_msg" placeholder="Type a message" />
                    <button class="msg_send_btn" type="button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
  <script>
    $(document).ready(function () {

        $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

        $.get('/fetch-users', function (data) {
            var users = data.users;
            $('.inbox_chat').empty();
            users.forEach(function (user) {
                var chatList = `
                    <button class="chat_list" data-receiver-id="${user.id}">
                        <div class="chat_people">
                            <div class="chat_img">
                                <img src="https://ptetutorials.com/images/user-profile.png" alt="user">
                            </div>
                            <div class="chat_ib">
                                <h5>${user.name}</h5>
                            </div>
                        </div>
                    </button>`;
                $('.inbox_chat').append(chatList);
            });
        }).fail(function (xhr, status, error) {
            console.error('Error fetching users:', error);
        });

        $('.inbox_chat').on('click', '.chat_list', function () {
            currentReceiverId = $(this).data('receiver-id'); // Store receiver ID globally
        console.log('Clicked on receiver ID:', currentReceiverId);
        setReceiverAndFetchChatHistory(currentReceiverId);
        });

    function setReceiverAndFetchChatHistory(receiverId) {
        $.post('{{ route('set.receiver') }}', { receiver_id: receiverId }, function (data) {
            console.log('Receiver ID set:', receiverId);
            fetchChatHistory(receiverId);
        });
    }

    $('.write_msg').keypress(function(event) {
        if (event.which == 13) {
            sendMessage();
        }
    });
    function prependMessage(message) {
        var msgHtml = '<div class="outgoing_msg">' +
                        '<div class="sent_msg">' +
                        '<p>' + message + '</p>' +
                        '<span class="time_date">Now</span>' +
                      '</div>' +
                      '</div>';

        $('.msg_history').append(msgHtml);
        $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight); // Scroll to bottom
    }
    function sendMessage() {
        var message = $('.write_msg').val();
        if (!message.trim()) return; 

        if (!currentReceiverId) {
            alert('Please select a user to chat with.');
            return;
        }

        $.post('{{ route('send.message') }}', {
            receiver_id: currentReceiverId, 
            message: message
        }, function (data) {
            console.log(data.message);
            $('.write_msg').val('');
            prependMessage(message);
            fetchChatHistory(currentReceiverId);
        }).fail(function (xhr, status, error) {
            console.error('Error sending message:', error);
        });
    }

    // Fetch chat history function
    function fetchChatHistory(receiverId) {
    var loggedUserId = '{{ auth()->id() }}'; 

    $.get('/fetch-chat-history/' + receiverId, function(data) {
        $('.msg_history').empty();

        data.messages.forEach(function(message) {
            var isOutgoing = message.sender_id == loggedUserId;
            var msgHtml;

            if (isOutgoing) {
                msgHtml = '<div class="outgoing_msg">' +
                            '<div class="sent_msg">' +
                            '<p>' + message.message + '</p>' + // Updated property name
                            '<span class="time_date"> ' + message.created_at + '</span> </div>' +
                          '</div>';
            } else {
                msgHtml = '<div class="incoming_msg">' +
                            '<div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="user"> </div>' +
                            '<div class="received_msg">' +
                            '<div class="received_withd_msg">' +
                            '<p>' + message.message + '</p>' + 
                            '<span class="time_date"> ' + message.created_at + '</span></div>' +
                            '</div>' +
                          '</div>';
            }

            $('.msg_history').prepend(msgHtml);
            $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);
        });
    }).fail(function(xhr, status, error) {
        console.error('Error fetching chat history:', error);
        alert('An error occurred while fetching chat history. Please try again later.');
    });
}


    // Initialize Laravel Echo with Pusher
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env('PUSHER_APP_KEY') }}',
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
    });

    Echo.private('chat.{{ auth()->id() }}')
        .listen('MessageSent', (e) => {
            console.log('Received message:', e.message);

            alert("New message from " + message.sender_id + ": " + message.message);

        });
});

</script>

@endsection