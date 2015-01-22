

<!-- VIDEO SLIDER -->
<section class="slider">
    <div class="container">
        <div id="myCarousel" class="carousel slide" data-ride="carousel">

            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <?php $i = 0; ?>
                @foreach($videos as $video)
                <?php
                if ($i == 0) {
                    ?>
                    <div class="item active">
                        <video id="player_a" class="projekktor player" title="{{$video->name}}" width="760" height="386" src="{{$video->youtube_url}}" type="video/youtube" controls></video>

                    </div><!-- End Item -->
                    <?php
                    $i++;
                } else {
                    ?>
                    <div class="item">
                        <video id="player_b" class="projekktor player" title="{{$video->name}}" width="760" height="386" src="{{$video->youtube_url}}" type="video/youtube" controls></video>

                    </div><!-- End Item -->
                    <?php
                    $i++;
                }
                ?>
                @endforeach
                <div class="item">
                    <img src="http://placehold.it/760x400/dddddd/333333">
                    <div class="carousel-caption">
                        <h4><a href="#">tempor invidunt ut labore et dolore</a></h4>
                        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                    </div>
                </div><!-- End Item -->

                <div class="item">
                    <img src="http://placehold.it/760x400/999999/cccccc">
                    <div class="carousel-caption">
                        <h4><a href="#">magna aliquyam erat, sed diam voluptua</a></h4>
                        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                    </div>
                </div><!-- End Item -->

                <div class="item">
                    <img src="http://placehold.it/760x400/dddddd/333333">
                    <div class="carousel-caption">
                        <h4><a href="#">tempor invidunt ut labore et dolore magna aliquyam erat</a></h4>
                        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                    </div>
                </div><!-- End Item -->

            </div><!-- End Carousel Inner -->


            <ul class="list-group col-sm-4">
                <?php $i = 0; ?>
                @foreach($videos as $video)
                <?php
                if ($i == 0) {
                    ?>
                    <li data-target="#myCarousel" data-slide-to="{{$i}}" class="list-group-item active"><h4>{{$video->name}}</h4></li>
                    <?php
                    $i++;
                } else if ($i < 5) {
                    ?>
                    <li data-target="#myCarousel" data-slide-to="{{$i}}" class="list-group-item"><h4>{{$video->name}}</h4></li>
                    <?php
                    $i++;
                }
                ?>
                @endforeach
            </ul>

            <!-- Controls -->
            <div class="carousel-controls">
                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </div>

        </div>
    </div>
</section>

<div class="container">

    <div class='row'>
        <h2>Featured Videos</h2>
        <div class='col-md-12'>
            <div class="carousel slide media-carousel" id="media">
                <div class="carousel-inner">

                    <div class="item active">
                        <div class="row">
                            @for($i=0;$i<4;$i++)
                            <?php
                            if (isset($videos[$i])) {
                                ?>
                                <div class="col-md-3">
                                    <a class="thumbnail " href="#"><img alt="" class="img-responsive" style="width:100%" src="{{$videos[$i]->image}}"></a>
                                    <div class="carousel-caption">
                                        <span>
                                            {{$videos[$i]->name }}
                                        </span>   
                                        <p>
                                            Views: 0 <br>
                                            Likes: 0
                                        </p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            @endfor
                        </div>
                    </div>
                    <?php
                    $k = 1;
                    $j = 4;
                    ?>
                    @while($k)
                    <?php
                    if ((isset($videos[$j])) && (isset($videos[$j + 1])) && (isset($videos[$j + 2])) && (isset($videos[$j + 3]))) {
                        ?>
                        <div class="item">

                            <div class="row">
                                @for($i=$j;$i<($j+4);$i++)
                                <?php
                                if (isset($videos[$i])) {
                                    ?>
                                    <div class="col-md-3">
                                        <a class="thumbnail " href="#"><img alt="{{$videos[$i]->image}}" class="img-responsive" style="width:100%"  src="{{$videos[$i]->image}}"></a>
                                        <div class="carousel-caption">
                                            <span>
                                                {{$videos[$i]->name }}
                                            </span>     
                                            <p>
                                                Views: 0 <br>
                                                Likes: 0 
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>          
                                @endfor
                                <?php $j+=4; ?>
                            </div>
                        </div>
                        <?php
                    } else {
                        $k = 0;
                    }
                    ?>
                    @endwhile

                </div>
                <a data-slide="prev" href="#media" class="left carousel-control">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a data-slide="next" href="#media" class="right carousel-control">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </div> 
        </div>
    </div>
</div>



<div class="container">

    <div class='row'>
        <h2>Recent Videos</h2>
        <div class='col-md-12'>
            <div class="carousel slide media-carousel" id="mediaR">
                <div class="carousel-inner">

                    <div class="item active">
                        <div class="row">
                            @for($i=0;$i<4;$i++)
                            <?php
                            if (isset($videos[$i])) {
                                ?>
                                <div class="col-md-3">
                                    <a class="thumbnail " href="#"><img alt="" class="img-responsive" style="width:100%" src="{{$videos[$i]->image}}"></a>
                                    <div class="carousel-caption">
                                        <span>
                                            {{$videos[$i]->name }}
                                        </span>   
                                        <p>
                                            Views: 0 <br>
                                            Likes: 0
                                        </p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            @endfor
                        </div>
                    </div>
                    <?php
                    $k = 1;
                    $j = 4;
                    ?>
                    @while($k)
                    <?php
                    if ((isset($videos[$j])) && (isset($videos[$j + 1])) && (isset($videos[$j + 2])) && (isset($videos[$j + 3]))) {
                        ?>
                        <div class="item">

                            <div class="row">
                                @for($i=$j;$i<($j+4);$i++)
                                <?php
                                if (isset($videos[$i])) {
                                    ?>
                                    <div class="col-md-3">
                                        <a class="thumbnail " href="#"><img alt="{{$videos[$i]->image}}" class="img-responsive" style="width:100%"  src="{{$videos[$i]->image}}"></a>
                                        <div class="carousel-caption">
                                            <span>
                                                {{$videos[$i]->name }}
                                            </span>     
                                            <p>
                                                Views: 0 <br>
                                                Likes: 0 
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>          
                                @endfor
                                <?php $j+=4; ?>
                            </div>
                        </div>
                        <?php
                    } else {
                        $k = 0;
                    }
                    ?>
                    @endwhile

                </div>
                <a data-slide="prev" href="#mediaR" class="left carousel-control">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a data-slide="next" href="#mediaR" class="right carousel-control">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </div> 
        </div>
    </div>
</div>


