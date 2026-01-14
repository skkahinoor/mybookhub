<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #122168 0%, #e55916 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .error-code {
            font-size: clamp(4rem, 12vw, 8rem);
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255,255,255,0.3);
        }

        .message {
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            margin-bottom: 1.5rem;
            opacity: 0.9;
            line-height: 1.4;
        }

        .subtext {
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            margin-bottom: 2.5rem;
            opacity: 0.7;
        }

        .home-btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .home-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
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

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }
            
            .home-btn {
                padding: 0.9rem 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1 class="message">Oops! Page Not Found</h1>
        <p class="subtext">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ url('/') }}" class="home-btn">Go Home</a>
    </div>
</body>
</html>


