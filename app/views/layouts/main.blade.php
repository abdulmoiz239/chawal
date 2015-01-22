<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MiddleEastVideo</title>

        <!-- Bootstrap Styles -->
        <link href="{{URL::to('mev/css/bootstrap.css')}}" rel="stylesheet" />
        <!-- Custom Styles -->
        <link href="{{URL::to('mev/css/style.css')}}" rel="stylesheet" />
        <!-- FontAwesome Icons -->
        <link href="{{URL::to('mev/css/font-awesome.css')}}" rel="stylesheet" />
        <link rel="stylesheet" href="{{URL::to('packages/css/theme/maccaco/projekktor.style.css')}}" type="text/css" media="screen" />

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- HEADER -->
        <header class="header">
      <div class="container">
        <div class="row">
          <h2>
            MEV
          </h2>

          <nav class="navbar navbar-default" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">
                  Toggle navigation
                </span>
                <span class="icon-bar">
                </span>
                <span class="icon-bar">
                </span>
                <span class="icon-bar">
                </span>
              </button>
              
            </div>
            
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li>
                  <a href="#">
                    Home
                  </a>
                </li>
                <li>
                  <a href="#">
                    Contact
                  </a>
                </li>
                <li>
                  <a href="#">
                    Shop
                  </a>
                </li>
                
              </ul>
              
              <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    Login
                    <b class="caret">
                    </b>
                  </a>
                  <ul class="dropdown-menu login">
                    <li>
                      
                      
                      <div id="loginbox" style="width: 380px; margin: 0px;padding: 0px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8">
                        
                        <div class="panel panel-info" >
                          <div class="panel-heading">
                            <div class="panel-title">
                              Sign In
                            </div>
                            <div style="float:right; font-size: 80%; position: relative; top:-10px">
                              <a href="#">
                                Forgot password?
                              </a>
                            </div>
                          </div>
                          
                          
                          <div style="padding-top:30px" class="panel-body" >
                            
                            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                            </div>
                            
                      <form id="loginform" class="form-horizontal" role="form">
                        
                        <div style="margin-bottom: 25px" class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-user">
                            </i>
                          </span>
                          <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                        </div>
                        <div style="margin-bottom: 25px" class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-lock">
                            </i>
                          </span>
                          <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                        </div>
                        <div class="input-group">
                          <div class="checkbox">
                            <label>
                              <input id="login-remember" type="checkbox" name="remember" value="1">
                              Remember me
                            </label>
                          </div>
                        </div>
                        <div style="margin-top:10px" class="form-group">
                          <!-- Button -->
                          <div class="col-sm-12 controls">
                            <a id="btn-login" href="#" class="btn btn-success">
                              Login  
                            </a>
                            <a id="btn-fblogin" href="#" class="btn btn-primary">
                              Login with Facebook
                            </a>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="col-md-12 control">
                            <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                              Don't have an account! 
                              <a href="#" onClick="$('#loginbox').hide(); $('#signupbox').show()">
                                Sign Up Here
                              </a>
                            </div>
                          </div>
                        </div>
                      </form>
                   </div>
                </div>
             </div>
                  <div id="signupbox" style="display:none; margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                      <div class="panel-heading">
                        <div class="panel-title">
                          Sign Up
                        </div>
                        <div style="float:right; font-size: 85%; position: relative; top:-10px">
                          <a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">
                            Sign In
                          </a>
                        </div>
                      </div>
                      <div class="panel-body" >
                        <form id="signupform" class="form-horizontal" role="form">
                          
                          <div id="signupalert" style="display:none" class="alert alert-danger">
                            <p>
                              Error:
                            </p>
                            <span>
                            </span>
                          </div>
                          <div class="form-group">
                            <label for="email" class="col-md-3 control-label">
                              Email
                            </label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" name="email" placeholder="Email Address">
                            </div>
                          </div>
                          
                          <div class="form-group">
                            <label for="firstname" class="col-md-3 control-label">
                              First Name
                            </label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" name="firstname" placeholder="First Name">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="lastname" class="col-md-3 control-label">
                              Last Name
                            </label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" name="lastname" placeholder="Last Name">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="password" class="col-md-3 control-label">
                              Password
                            </label>
                            <div class="col-md-9">
                              <input type="password" class="form-control" name="passwd" placeholder="Password">
                            </div>
                          </div>
                          
                          <div class="form-group">
                            <label for="icode" class="col-md-3 control-label">
                              Invitation Code
                            </label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" name="icode" placeholder="">
                            </div>
                          </div>
                          
                          <div class="form-group">
                            <!-- Button -->
                            
                            <div class="col-md-offset-3 col-md-9">
                              <button id="btn-signup" type="button" class="btn btn-info">
                                <i class="icon-hand-right">
                                </i>
                                &nbsp; Sign Up
                              </button>
                              <span style="margin-left:8px;">
                                or
                              </span>
                              
                            </div>
                          </div>
                          
                    <div style="border-top: 1px solid #999; padding-top:20px"  class="form-group">
                     <div class="col-md-offset-3 col-md-9">
                          <button id="btn-fbsignup" type="button" class="btn btn-primary">
                                <i class="icon-facebook">
                                </i>
                                Sign Up with Facebook
                         </button>
                    </div>           
                </div>              
            </form>
       </div>
    </div>
</div>
                  
            </li>

            </ul>
          </li>
            
            <li>
              <form class="navbar-form" role="search">
                <div class="form-group">
                  <input type="text" class="form-control col-lg" placeholder="Search">
                  
                </div>
                <button class="glyphicon glyphicon-search form-control">
                </button>
              </form>
        </li>
     </ul>
 </div>
          <!-- /.navbar-collapse -->      
        </nav>
    </div>
</div>      


<!--Ad Start-->
<div class="container">
<div class="col-md-12">
<div class="banner-header">
<img src="mev/img/banner.gif" alt="Advertising Banner" />
</div>
</div>
</div>
<!--Ad End-->
</header>

<!-- ./HEADER -->

<!-- ./HEADER -->
<div class="content">
    {{$content}}
</div>


<!-- Ad Space -->
<section class="advertising-bottom">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center">
                <img src="mev/img/ad.gif" height="300" alt="Advertisement" />
            </div>
            <div class="col-md-6 text-center">
                <img src="mev/img/ad2.jpg" height="300" alt="Advertisement" />
            </div>
        </div>
    </div>
</section>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                MiddleEastVideo &copy; 2015 - All Rights Reserved. <a href="#">Privacy</a> | <a href="#">Terms</a> | <a href="#">About</a> | <a href="#">Submit Video</a>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{URL::to('mev/js/jquery.min.js')}}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{URL::to('mev/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{URL::to('packages/js/projekktor-1.2.35r319.min.js')}}"></script>

<script>
                                                            $(window).load(function () {
                                                                
                                                                var boxheight = $('#myCarousel .carousel-inner').innerHeight();
                                                                var itemlength = $('#myCarousel .item').length;
                                                                var triggerheight = Math.round(boxheight / itemlength + 1);
                                                                $('#myCarousel .list-group-item').outerHeight('77.9');
                                                            });

                                                            $(document).ready(function () {
                                                                projekktor(
                                                                        '.player', // destination-container-selector-fuzz a la jQuery
                                                                        {
                                                                            autoplay: false
                                                                        }, // an empty Projekktor-ccofig-object
                                                                function (player) { // "onready" callback -
                                                                    $('#projekktorver').html(player.getPlayerVer());
                                                                }
                                                                );

                                                                /* projekktor(
                                                                 '#player_a', // destination-container-selector-fuzz a la jQuery
                                                                 {
                                                                 }, // an empty Projekktor-ccofig-object
                                                                 function(player) { // "onready" callback -
                                                                 $('#projekktorver').html( player.getPlayerVer() );
                                                                 }
                                                                 );*/
                                                                var clickEvent = false;
                                                                $('#myCarousel').carousel({
                                                                    interval: 0,
                                                                }).on('click', '.list-group li', function () {
                                                                    clickEvent = true;
                                                                    $('.list-group li').removeClass('active');
                                                                    $(this).addClass('active');
                                                                }).on('slid.bs.carousel', function (e) {
                                                                    if (!clickEvent) {
                                                                        var count = $('.list-group').children().length - 1;
                                                                        var current = $('.list-group li.active');
                                                                        current.removeClass('active').next().addClass('active');
                                                                        var id = parseInt(current.data('slide-to'));
                                                                        if (count == id) {
                                                                            $('.list-group li').first().addClass('active');
                                                                        }
                                                                    }
                                                                    clickEvent = false;
                                                                });
                                                            });


</script>
</body>

</html>