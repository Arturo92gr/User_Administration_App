@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verification Status') }}</div>

                <div class="card-body">
                    @auth
                        @if (auth()->user()->hasVerifiedEmail())
                            <h4 class="text-success">{{ __('Estás verificado') }}</h4>
                        @else
                            <h4 class="text-warning">{{ __('Aún no estás verificado, comprueba tu correo') }}</h4>
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0">
                                    {{ __('Reenviar email de verificación') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <h4>{{ __('Debes iniciar sesión para ver el estado de verificación') }}</h4>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection