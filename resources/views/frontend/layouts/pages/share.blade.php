@extends('frontend.app')
@section('title', "Share Page")

@section('content')
<style>
    /* Entry animation for the card */
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Background animation */
    @keyframes backgroundMove {
        0% {
            background-position: 0% 50%;
        }
        100% {
            background-position: 100% 50%;
        }
    }

    .share-section {
        background: linear-gradient(135deg, #f0f4f8, #e9eff5);
        background-size: 200% 200%;
        animation: backgroundMove 10s ease infinite alternate;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .share-card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        animation: fadeInUp 1s ease;
        transition: transform 0.3s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .share-card::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(13,110,253,0.05) 0%, transparent 70%);
        animation: pulse 5s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.7;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.3;
        }
    }

    .share-card:hover {
        transform: translateY(-10px);
    }

    .share-card h2 {
        font-weight: 700;
        color: #2c3e50;
    }

    .share-card p {
        color: #555;
        font-size: 18px;
        margin-top: 20px;
    }

    .download-btn {
        margin-top: 30px;
        padding: 12px 25px;
        background: linear-gradient(90deg, #0d6efd, #6610f2);
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.4s ease;
        font-weight: bold;
    }

    .download-btn:hover {
        background: linear-gradient(90deg, #6610f2, #0d6efd);
        transform: scale(1.05);
    }
</style>

<section class="share-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card share-card">
                    <h2>Please Download Our App</h2>
                    <p class="lead">Download our app and stay connected with all the latest features and updates!</p>
                    <a href="https://drive.google.com/drive/folders/1Q5jAcoQqrQhfZQZJhrv5bYX9V1JY3tzb" class="download-btn">Download Now</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
