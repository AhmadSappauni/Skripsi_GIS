<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Itinerary Banjarbakula</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

    <ul class="slideshow">
        @foreach($slides as $foto)
            <li style="background-image: url('{{ asset('images/' . $foto) }}')"></li>
        @endforeach
    </ul>
    <div class="video-overlay"></div> 

    <div class="container"></div>

    <div class="container">
        <img src="{{ asset('images/logo skripsi (2).png') }}" alt="Logo ULM" class="logo">

        <h1>Smart <span>Itinerary</span><br>Banjarbakula</h1>
        
        <p class="subtitle">
            Jelajahi keindahan Kalimantan Selatan dengan rencana perjalanan<br>
            yang dioptimalkan oleh <strong>Algoritma Greedy</strong>.
        </p>

        <div class="feature-box">
            <div class="feature"> Optimasi Budget</div>
            <div class="feature"> Rute Terdekat</div>
            <div class="feature"> Peta Interaktif</div>
        </div>

        <a href="{{ route('app.peta') }}" class="btn-start">
             Mulai Rencanakan Liburan
        </a>
    </div>

    <div class="footer">
        &copy; 2025 Ahmad Sappauni<br>
        Fakultas Keguruan dan Ilmu Pendidikan ULM
    </div>

</body>
</html>