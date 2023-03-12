<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    /* Mailbox a göre ayanlandı */

    .row {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
     }

    .card  {
        width: 500px !important;
        height: 500px !important;
        border-radius: 10px !important;
        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2) !important;
        background-color: #fff !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
     } 

    .card-header  {
        width: 100% !important;
        height: 100px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
     }

    .card-body  {
        width: 100% !important;
        height: 300px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
     }

    .logo  {
        width: 100px !important;
        height: 100px !important;
     }

    button  {
        width: 300px !important;
        height: 50px !important;
        border-radius: 10px !important;
        background-color: #000 !important;
        color: #fff !important;
        font-size: 18px !important;
        font-weight: bold !important;
        border: none !important;
        cursor: pointer !important;
     }

    button:hover  {
        background-color: #fff !important;
        color: #000 !important;
        border: 1px solid #000 !important;
     }

    label  {
        font-size: 18px !important;
        font-weight: bold !important;
        margin-bottom: 20px !important;
    }


  
    </style>

</head>

<body>
    <div class="row">
        <div class="card">
            <div class="card-header">
                <img class="logo" src="{{asset('imgs/logo.png')}}" alt="">
            </div>
            <div class="card-body">
                <label>Merhaba {{$user?->name}}</label>
                <label>Üyeliğinizi tamamlamak için aşağıdaki linke tıklayınız.</label>
                <form action="{{$data['link']}}">
                    <button onclick="go()">Emailinizi Onaylamak İçin Tıklayınız</button>
                </form>
                <label style="font-size: 18px;">Sitemize Kaydolan Siz Değilseniz Bu Maili Dikkate Almayınız.</label>
            </div>
        </div>
    </div>
</body>



<script>
    document.getElementsByClassName('row')[0].style.height = window.innerHeight + 'px';
</script>

</html>