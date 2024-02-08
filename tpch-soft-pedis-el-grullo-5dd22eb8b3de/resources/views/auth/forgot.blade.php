@extends('layouts.auth')

@section('title', 'Recuperar Clave')

@section('content')
<div class="full-height d-flex">

    <svg class="p-fixed bottom-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
        <path fill="var(--primary)" fill-opacity="1" d="M0,64L60,90.7C120,117,240,171,360,208C480,245,600,267,720,256C840,245,960,203,1080,197.3C1200,192,1320,224,1380,240L1440,256L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
    </svg>

    <div class="m-auto card w-33 restore-pass-box">
        
        <img src="/img/branding/icon.png" class="d-block m-auto icon-xxl" alt="logo"/>
        <h1 class="text-center mb-1">Recuperar clave</h1>
        <form action="{{ route('restore_pass') }}" method="POST" class="form-block">
            @csrf
            <input name="email" class="form-control" placeholder="Ingresa tu email.." value="{{ old('email') }}" type="email" required>
            <button class="btn btn-main-primary btn-block">Recuperar</button>
        </form>

        <div class="mt-2">
            <p><a href="{{ route('login') }}">&larr; Volver para ingresar</a></p>
        </div>
        @if (Session::has('error'))
            <div class="mt-1 bg-red round-sm px-1 pt-05 pb-05 text-heavy text-white">
                <p>🤔 {{ Session::get('error') }}</p>
            </div>
        @endif

        @if (Session::has('message'))
            <div class="mt-1 bg-green round-sm px-1 pt-05 pb-05 text-heavy text-white">
                <p>😎 {{ Session::get('message') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection