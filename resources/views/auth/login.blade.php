@extends('master')

@section('title', '- Login')





@section('container-body')
{{  $errors->first('email') }}
{{  $errors->first('username') }}
  <div class="div-signin">
    <div>
      <img class="center-block img-signin img-circle img-responsive" src="/images/login-avatar.png">
    </div>
    
      {!! Form::open(['url' => 'login', 'accept-charset'=>'utf-8', 'class'=>'form-signin']) !!}
    


      <label class="sr-only" for="inputEmail">Username</label>
      <input id="inputEmail" class="form-control" type="text" 
      <?php 

        echo !empty(old('email')) ? 'value="'.old('email').'"' : 'autofocus=""';

      ?> required="" placeholder="Username" name="email">

      <label class="sr-only" for="inputPassword">Password</label>
      @if($errors->has('email'))
        <div class="has-error">
        <input id="inputPassword" class="form-control" type="password" required="" autofocus="" placeholder="Password" name="password">
        <p class="text-danger">username or password you entered is incorrect.</p>
        </div>
      @else
        <input id="inputPassword" class="form-control" type="password" required="" placeholder="Password" name="password">
      @endif

      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      {!! Form::close() !!}
  </div>



@endsection