@extends('layouts.auth')

@section('title', 'Ingreso')

@section('content')
<div class="full-height d-flex">

    <svg class="p-fixed bottom-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
        <path fill="var(--primary)" fill-opacity="1" d="M0,64L60,90.7C120,117,240,171,360,208C480,245,600,267,720,256C840,245,960,203,1080,197.3C1200,192,1320,224,1380,240L1440,256L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
    </svg>

    <div class="m-auto card w-33 login-box">

        <img src="/img/branding/icon.png" class="d-block m-auto icon-xxl" alt="logo"/>
        <h1 class="text-center mt-auto mb-1">Iniciar sesión</h1>
        <form action="{{ route('auth') }}" method="POST" autocomplete="new-password" class="form-block">
            @csrf
            <input name="user" class="form-control" autocomplete="new-password" placeholder="Ingresa tu alias o email" value="{{ old('user') }}" type="text" required>
            <input name="password" class="form-control" autocomplete="new-password" placeholder="Ingresa tu clave.." type="password" required>
            <button class="btn btn-block">Ingresar</button>
        </form>

        <div class="mt-2">
            <p><a href="{{ route('forgot_restore') }}">¿Olvidaste tu clave?</a></p>
        </div>
        @if (Session::has('error'))
            <div class="mt-1 bg-red round-sm px-1 pt-05 pb-05 text-heavy text-white">
                <p>🤔 {{ Session::get('error') }}</p>
            </div>
        @endif

    </div>
</div>
@endsection