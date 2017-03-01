
<!doctype html>
<html lang="en">
<head>
  
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> 
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>GI BM @yield('title')</title>

  <link rel="shortcut icon" type="image/x-icon" href="/images/g.png" />
@if(app()->environment() == 'local')
<!--
  <link rel="stylesheet" href="/css/normalize-3.0.3.min.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css">
  <link rel="stylesheet" href="/css/bootstrap-3.3.5.css">
  <link rel="stylesheet" href="/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="/css/bootstrap-datetimepicker-4.17.37.min.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <link rel="stylesheet" href="/css/bt-override.css">
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/dropbox.css">
-->

  
  <link rel="stylesheet" href="/css/styles-all.min.css">
@else 
  <link rel="stylesheet" href="/css/styles-all.min.css">
@endif
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
@section('css-external')

@show

  
</head>
<body class="@yield('body-class')">
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
    	
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    
      <a class="navbar-brand" href="/">
        <img src="data:image/png;base64,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" 
        class="img-responsive header-logo">
      </a>
      <p class="navbar-text hidden-xs" style="font-size: 20px; margin: 11px 0px 11px -10px;">
        <em style=" color: #3c763d;">
          <span>Manager's Module</span>
          <span style=" color: #d6e9c6;"> Beta</span>
        </em>
      </p>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      @yield('navbar-1')
      @yield('navbar-2')
    </div>
  </div>
</nav>

@section('container-body')

@show


@section('js-external')

@show



@if(app()->environment() == 'production')
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-68152291-3', 'auto');
  ga('send', 'pageview');

</script>
@endif

</body>
</html>