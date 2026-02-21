<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daily Eggspress</title>
    @vite('resources/css/welcome_style.css')
</head>
<body>
    <div class="the-header">
        <div class="logo-container">
            <div class="logo-box">
                <span class="egg">
                    <img src="/images/eggleft.png" id="e-left">
                    <img src="/images/adminchick.png" id="chick">
                    <img src="/images/eggright.png" id="e-right">
                </span>
                <span class="title-logo">
                    <div class="egg-crack">
                        <img src="/images/eggleft.png" id="egg-crack-left">
                        <img src="/images/eggright.png" id="egg-crack-right">
                    </div>
                    <h1>myBL&nbsp;&nbsp;G</h1>
                    <img src="/images/yolk.png" id="yolk">
                    <p>The Daily Eggspress</p>
                </span>
            </div>
        </div>
    </div>
    <div class="the-content">
        
        <div class="log-window"></div>
    </div>
    <div class="the-footer"></div>
    @vite('resources/js/welcome_script.js')
</body>
</html>