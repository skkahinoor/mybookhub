<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Website is under maintenance">
    <title>Maintenance Mode - BookHub</title>
    
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

        
        .maintenance-container {
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
        
        .maintenance-icon {
            font-size: 6rem;
            margin-bottom: 30px;
            animation: rotate 3s linear infinite;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
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
            opacity: 0.95;
            animation: fadeInUp 1s ease-out;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.7), 0 0 15px rgba(0,0,0,0.4);
            color: #fff;
        }
        
        .maintenance-message {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeIn 1.5s ease-out;
        }
        
        .maintenance-message p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0;
            color: #fff;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
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
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            
            .subtitle {
                font-size: 1.2rem;
            }
            
            .maintenance-icon {
                font-size: 4rem;
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
    
    <div class="maintenance-container">
        @if(isset($logos) && $logos && isset($logos->logo) && $logos->logo)
            <div class="logo-container">
                <img src="{{ asset('uploads/logos/' . $logos->logo) }}" alt="Logo">
            </div>
        @endif
        
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        
        <h1>We'll Be Back Soon!</h1>
        <p class="subtitle">Our website is currently undergoing scheduled maintenance</p>
        
        <div class="maintenance-message">
            <p>
                <i class="fas fa-info-circle"></i> We're working hard to improve your experience. 
                Please check back shortly. We apologize for any inconvenience.
            </p>
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
</body>
</html>
