@if(app()->environment() == 'local')
<?php
$debugbar = new DebugBar\StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();
?>
@endif
<!doctype html>
<html lang="en">
<head>
  
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> 
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>Giligan's Restaurant @yield('title')</title>

  <link rel="shortcut icon" type="image/x-icon" href="/images/g.png" />
@if(app()->environment() == 'local')
  <link rel="stylesheet" href="/css/normalize-3.0.3.min.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css">
  <link rel="stylesheet" href="/css/bootstrap-3.3.5.css">
  <link rel="stylesheet" href="/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <link rel="stylesheet" href="/css/bt-override.css">
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/dropbox.css">

  <link rel="stylesheet" type="text/css" href="/Resources/vendor/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="/Resources/vendor/highlightjs/styles/github.css">
  <link rel="stylesheet" type="text/css" href="/Resources/debugbar.css">
  <link rel="stylesheet" type="text/css" href="/Resources/widgets.css">
  <link rel="stylesheet" type="text/css" href="/Resources/openhandler.css">
  <script type="text/javascript" src="/Resources/vendor/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="/Resources/vendor/highlightjs/highlight.pack.js"></script>
  <script type="text/javascript" src="/Resources/debugbar.js"></script>
  <script type="text/javascript" src="/Resources/widgets.js"></script>
  <script type="text/javascript" src="/Resources/openhandler.js"></script>
  <script type="text/javascript">jQuery.noConflict(true);</script>
@else 
  <link rel="stylesheet" href="/css/styles-all.min.css">
@endif

  
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
        <img src="/images/giligans-header.png" class="img-responsive header-logo">
      </a>
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
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-67989352-1', 'auto');
  ga('send', 'pageview');
</script>
@endif
@if(app()->environment() == 'local')
<?php echo $debugbarRenderer->render() ?>
@endif
</body>
</html>