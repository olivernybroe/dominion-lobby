@extends('layouts.app')

@section('content')
    <div class="container h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <div class="col-md-6">
                <form class="form-signin text-center" method="post" action="{{route('login')}}">
                    @csrf
                    <img class="mb-4 img-fluid" src="img/logo.jpg" alt="">
                    <h1 class="h3 mb-3 font-weight-normal">Dominion Admin Panel</h1>

                    <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                        <label class="col-md-6 control-label">Username</label>

                        <div class="col-md-12">
                            <input type="text" class="form-control" name="username" value="{{ old('username') ?: "s153558" }}">

                            @if ($errors->has('username'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="col-md-6 control-label">Password</label>

                        <div class="col-md-12">
                            <input type="password" class="form-control" name="password" value="1234">

                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="checkbox mb-3">
                        <label>
                            <input type="checkbox" value="remember-me"> Remember me
                        </label>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block font-weight-bold" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
@endsection
