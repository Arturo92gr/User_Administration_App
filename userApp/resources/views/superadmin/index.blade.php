@extends('layouts.app')
@use('Illuminate\Support\Facades\Auth')

@section('content')
<h1>Users Management</h1>
<br>
    <table class="table table-striped table-hover" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Email Verified</th>
                <th>Role</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <input type="checkbox" 
                            onchange="document.getElementById('verify-email-{{ $user->id }}').submit();" 
                            {{ $user->email_verified_at ? 'checked' : '' }}>
                        <form id="verify-email-{{ $user->id }}" 
                            action="{{ route('superadmin.verifyEmail', $user->id) }}" 
                            method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="verified" value="{{ $user->email_verified_at ? 0 : 1 }}">
                        </form>
                    </td>
                    <td>{{ $user->role }}</td>
                    <td><a href="{{ route('superadmin.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a></td>
                    <td>
                        @if($user->id !== 1)
                            <form id="delete-form-{{ $user->id }}" action="{{ route('superadmin.destroy', $user->id) }}" 
                                method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <a href="#" data-href="{{ route('superadmin.destroy', $user->id) }}" 
                            class="btn btn-sm btn-danger borrar">Delete</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    <script src="{{url('assets/scripts/script.js')}}"></script>
@endsection