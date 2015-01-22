<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <title>Projekktor - simply mighty video</title>
    <style type="text/css">
	body { background-color: #fdfdfd; padding: 0 20px; color:#000; font: 13px/18px monospace; width: 800px;}
	a { color: #360; }
	h3 { padding-top: 20px; }
    </style>

    <!-- Load player theme -->
    <link rel="stylesheet" href="{{URL::to('packages/css/theme/maccaco/projekktor.style.css')}}" type="text/css" media="screen" />

    <!-- Load jquery -->
    <script type="text/javascript" src="{{URL::to('packages/js/jquery-1.9.1.min.js')}}"></script>

    <!-- load projekktor -->
    <script type="text/javascript" src="{{URL::to('packages/js/projekktor-1.2.35r319.min.js')}}"></script>

</head>
<body>
   
    <video id="player_a" class="projekktor" poster="media/intro.png" title="this is Projekktor" width="640" height="385" controls>
        
        <source src="Samsung LED TV Demo Video 2014 - Motion - Full HD 1080p High Definition.mp4" type="video/mp4" />
        
    </video>

    <h2>Just a simple YouTube Test</h2>
<video id="player_b" class="projekktor" title="Projekktor YouTube Test" width="640" height="385" src="https://www.youtube.com/watch?v=AVDtu0SIjOE" type="video/youtube" controls></video>


    <script type="text/javascript">
	$(document).ready(function() {
             projekktor(
                '#player_a', // destination-container-selector-fuzz a la jQuery
                {
                    
		}, // an empty Projekktor-ccofig-object
                function(player) { // "onready" callback -
                    $('#projekktorver').html( player.getPlayerVer() );
                }
	    );
	    
                projekktor(
                '#player_b', // destination-container-selector-fuzz a la jQuery
                {
                    
		}, // an empty Projekktor-ccofig-object
                function(player) { // "onready" callback -
                    $('#projekktorver').html( player.getPlayerVer() );
                }
	    );
	});
    </script>

    <h3 style="color: red;">Already using Projekktor? Please don&acute;t forget to read the <a href="http://www.projekktor.com/changelog.php">changelog</a>.</h3>

</body>
</html>
