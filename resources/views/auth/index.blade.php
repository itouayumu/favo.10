@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <img src="{{ asset('img/fs11.png') }}" alt="husen">

<div class="background-container">
    <img src="{{ asset('img/me09.png') }}" alt="background" class="backgroung-image">
    <div class="form-container">
        <form action="#" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="4" required></textarea><br>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>
@endsection
