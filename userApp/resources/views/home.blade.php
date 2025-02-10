@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{ __('You are logged in') }}

                    <div class="mt-4">
                        <a href="{{ route('verificado') }}">Verified email status</a>
                    </div>

                    <div class="mt-4">
                        <h5>Profile Management</h5>
                        <br>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="{{ route('profile.manage') }}" class="btn btn-primary">
                                    {{ __('Edit Profile') }}
                                </a>
                            </li>
                        </ul>
                        <div class="form-group mt-3">
                            <a href="{{ route('index') }}" class="btn btn-secondary">
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>        
                </div>
            </div>
        </div>
    </div>
</div>
@endsection