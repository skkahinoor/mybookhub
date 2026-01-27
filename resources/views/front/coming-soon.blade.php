<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="We're coming soon!">
    <title>Coming Soon - BookHub</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('{{ asset('front/images/maintainmode/maintainmode.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }
        
        body::before {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(3px);
}

        .coming-soon-container {
            text-align: center;
            color: #fff;
            padding: 40px 20px;
            max-width: 800px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .logo-container {
            margin-bottom: 40px;
        }
        
        .logo-container img {
            max-width: 200px;
            height: auto;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));
        }
        
        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 3px 3px 8px rgba(0,0,0,0.8), 0 0 20px rgba(0,0,0,0.5);
            animation: fadeInDown 1s ease-out;
            color: #fff;
        }
        
        .subtitle {
            font-size: 1.5rem;
            margin-bottom: 40px;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out;
        }
        
        .countdown {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 50px 0;
            flex-wrap: wrap;
            animation: fadeIn 1.5s ease-out;
        }
        
        .countdown-item {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px 25px;
            min-width: 120px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .countdown-number {
            font-size: 3rem;
            font-weight: 700;
            display: block;
            margin-bottom: 10px;
            color: #fff;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.8);
        }
        
        .countdown-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.95;
            color: #fff;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.7);
        }
        
        .social-links {
            margin-top: 50px;
            animation: fadeIn 2s ease-out;
        }
        
        .social-links a {
            color: #fff;
            font-size: 1.5rem;
            margin: 0 15px;
            transition: transform 0.3s ease;
            display: inline-block;
        }
        
        .social-links a:hover {
            transform: translateY(-5px) scale(1.2);
        }
        
        .notification-badge {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 15px 30px;
            display: inline-block;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: pulse 2s infinite;
            color: #fff;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            
            .subtitle {
                font-size: 1.2rem;
            }
            
            .countdown {
                gap: 15px;
            }
            
            .countdown-item {
                min-width: 90px;
                padding: 20px 15px;
            }
            
            .countdown-number {
                font-size: 2rem;
            }
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            top: 0;
            left: 0;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite;
        }
        
        .shape:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 5s;
        }
        
        .shape:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="coming-soon-container">
        @if(isset($logos) && $logos && isset($logos->logo) && $logos->logo)
            <div class="logo-container">
                <img src="{{ asset('uploads/logos/' . $logos->logo) }}" alt="Logo">
            </div>
        @endif
        
        <h1>Coming Soon</h1>
        <p class="subtitle">We're working hard to bring you something amazing. Stay tuned!</p>
        
        @if(isset($showCountdown) && $showCountdown == 1)
        <div class="countdown" id="countdown">
            <div class="countdown-item">
                <span class="countdown-number" id="days">00</span>
                <span class="countdown-label">Days</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="hours">00</span>
                <span class="countdown-label">Hours</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="minutes">00</span>
                <span class="countdown-label">Minutes</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="seconds">00</span>
                <span class="countdown-label">Seconds</span>
            </div>
        </div>
        @endif
        
        <div class="notification-badge">
            <i class="fas fa-bell"></i> We'll notify you when we're live!
        </div>
        
        <div class="social-links">
            @if(!empty($socialMedia['facebook']))
                <a href="{{ $socialMedia['facebook'] }}" target="_blank" rel="noopener noreferrer" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
            @endif
            @if(!empty($socialMedia['twitter']))
                <a href="{{ $socialMedia['twitter'] }}" target="_blank" rel="noopener noreferrer" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
            @endif
            @if(!empty($socialMedia['instagram']))
                <a href="{{ $socialMedia['instagram'] }}" target="_blank" rel="noopener noreferrer" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            @endif
            @if(!empty($socialMedia['linkedin']))
                <a href="{{ $socialMedia['linkedin'] }}" target="_blank" rel="noopener noreferrer" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            @endif
            @if(!empty($socialMedia['youtube']))
                <a href="{{ $socialMedia['youtube'] }}" target="_blank" rel="noopener noreferrer" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
            @endif
            @if(!empty($socialMedia['pinterest']))
                <a href="{{ $socialMedia['pinterest'] }}" target="_blank" rel="noopener noreferrer" title="Pinterest">
                    <i class="fab fa-pinterest"></i>
                </a>
            @endif
            @if(!empty($socialMedia['whatsapp']))
                <a href="{{ $socialMedia['whatsapp'] }}" target="_blank" rel="noopener noreferrer" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            @endif
            @if(!empty($socialMedia['telegram']))
                <a href="{{ $socialMedia['telegram'] }}" target="_blank" rel="noopener noreferrer" title="Telegram">
                    <i class="fab fa-telegram"></i>
                </a>
            @endif
        </div>
    </div>
    
    <script>
        @if(isset($showCountdown) && $showCountdown == 1)
        // Get target date from server
        const countdownDate = @json($countdownDate ?? '');
        const countdownTime = @json($countdownTime ?? '');
        
        let targetDate;
        if (countdownDate && countdownTime) {
            // Combine date and time in ISO format
            targetDate = new Date(countdownDate + 'T' + countdownTime);
        } else if (countdownDate) {
            // Only date provided, set time to end of day
            targetDate = new Date(countdownDate + 'T23:59:59');
        } else {
            // Default: 30 days from now
            targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + 30);
        }
        
        // Validate date
        if (isNaN(targetDate.getTime())) {
            // If invalid, default to 30 days from now
            targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + 30);
        }
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate.getTime() - now;
            
            if (distance < 0) {
                document.getElementById('countdown').innerHTML = '<div class="countdown-item"><span class="countdown-number">00</span><span class="countdown-label">We\'re Live!</span></div>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');
            
            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        }
        
        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown();
        @endif
    </script>
</body>
</html>
