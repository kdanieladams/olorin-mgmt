@if(session()->has('flash_message') || session()->has('flash_message_alert') || session()->has('flash_message_confirm'))
    <script>
        document.onreadystatechange = function(){
            @if(session()->has('flash_message'))
            sweetAlert({
                html: true,
                title: "{!! session('flash_message.title') !!}",
                text: "{!! session('flash_message.message') !!}",
                type: "{!! session('flash_message.type') !!}",
                timer: 3000,
                showConfirmButton: false
            });
            @endif

            @if(session()->has('flash_message_alert'))
            sweetAlert({
                html: true,
                title: "{!! session('flash_message_alert.title') !!}",
                text: "{!! session('flash_message_alert.message') !!}",
                type: "{!! session('flash_message_alert.type') !!}",
                confirmButtonText: "Ok",
                confirmButtonColor: "#337ab7" // $brand-primary
            });
            @endif

            @if(session()->has('flash_message_confirm'))
            sweetAlert({
                html: true,
                title: "{!! session('flash_message_confirm.title') !!}",
                text: "{!! session('flash_message_confirm.message') !!}",
                type: "{!! session('flash_message_confirm.type') !!}",
                confirmButtonText: "Do it Anyway!",
                confirmButtonColor: "#900",
                showCancelButton: true,
                cancelButtonText: 'Cancel'
            });
            @endif
        };
    </script>
@endif