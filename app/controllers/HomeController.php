<?php

class HomeController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Home Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */
    protected $layout = 'layouts.main';
    public function showWelcome() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        //$xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=95.141.28.53");
        //$country = $xml->geoplugin_countryName;
        //if ($country == "Pakistan") {
         //   return "download";
        //} else {
            $videos = Video::all();
            $this->layout->content =  View::make('index')->with('videos',$videos);
            
        //}
    }

    public function show() {
        $_REQUEST['videoid'] = Input::get('videoid');
        $_REQUEST['type'] = Input::get('type');
// YouTube Downloader PHP
// based on youtube-dl in Python http://rg3.github.com/youtube-dl/
// by Ricardo Garcia Gonzalez and others (details at url above)
//
// Takes a VideoID and outputs a list of formats in which the video can be
// downloaded
        $config['ThumbnailImageMode'] = 1;    // show thumbnail image by proxy from this server

        /*         * ********|| Video Download Link Configuration ||************** */
        #$config['VideoLinkMode']='direct'; // show only direct download link
        #$config['VideoLinkMode']='proxy'; // show only by proxy download link
        $config['VideoLinkMode'] = 'both'; // show both direct and by proxy download links

        /*         * ********|| features ||************** */
        $config['feature']['browserExtensions'] = true; // show links for install browser extensions? true or false

        /*         * ********|| Other ||************** */
        // Set your default timezone
        // use this link: http://php.net/manual/en/timezones.php
        date_default_timezone_set("Asia/Tehran");

        // Debug mode
        $debug = true; // debug mode on
        #$debug = false; // debug mode off


        /*         * ********|| Don't edit below ||************** */

        function curlGet($URL) {
            $ch = curl_init();
            $timeout = 3;
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            /* if you want to force to ipv6, uncomment the following line */
            //curl_setopt( $ch , CURLOPT_IPRESOLVE , 'CURLOPT_IPRESOLVE_V6');
            $tmp = curl_exec($ch);
            curl_close($ch);
            return $tmp;
        }

        /*
         * function to use cUrl to get the headers of the file 
         */

        function get_location($url) {
            $my_ch = curl_init();
            curl_setopt($my_ch, CURLOPT_URL, $url);
            curl_setopt($my_ch, CURLOPT_HEADER, true);
            curl_setopt($my_ch, CURLOPT_NOBODY, true);
            curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($my_ch, CURLOPT_TIMEOUT, 10);
            $r = curl_exec($my_ch);
            foreach (explode("\n", $r) as $header) {
                if (strpos($header, 'Location: ') === 0) {
                    return trim(substr($header, 10));
                }
            }
            return '';
        }

        function get_size($url) {
            $my_ch = curl_init();
            curl_setopt($my_ch, CURLOPT_URL, $url);
            curl_setopt($my_ch, CURLOPT_HEADER, true);
            curl_setopt($my_ch, CURLOPT_NOBODY, true);
            curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($my_ch, CURLOPT_TIMEOUT, 10);
            $r = curl_exec($my_ch);
            foreach (explode("\n", $r) as $header) {
                if (strpos($header, 'Content-Length:') === 0) {
                    return trim(substr($header, 16));
                }
            }
            return '';
        }

        function get_description($url) {
            $fullpage = curlGet($url);
            $dom = new DOMDocument();
            @$dom->loadHTML($fullpage);
            $xpath = new DOMXPath($dom);
            $tags = $xpath->query('//div[@class="info-description-body"]');
            foreach ($tags as $tag) {
                $my_description .= (trim($tag->nodeValue));
            }

            return utf8_decode($my_description);
        }

        ob_start(); // if not, some servers will show this php warning: header is already set in line 46...

        function clean($string) {
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
            return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        }

        function formatBytes($bytes, $precision = 2) {
            $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, $precision) . '' . $units[$pow];
        }

        function is_chrome() {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match("/like\sGecko\)\sChrome\//", $agent)) { // if user agent is google chrome
                if (!strstr($agent, 'Iron')) // but not Iron
                    return true;
            }
            return false; // if isn't chrome return false
        }

        if (isset($_REQUEST['videoid'])) {
            $my_id = $_REQUEST['videoid'];
            if (strlen($my_id) > 11) {
                $url = parse_url($my_id);
                $my_id = NULL;
                if (is_array($url) && count($url) > 0 && isset($url['query']) && !empty($url['query'])) {
                    $parts = explode('&', $url['query']);
                    if (is_array($parts) && count($parts) > 0) {
                        foreach ($parts as $p) {
                            $pattern = '/^v\=/';
                            if (preg_match($pattern, $p)) {
                                $my_id = preg_replace($pattern, '', $p);
                                break;
                            }
                        }
                    }
                    if (!$my_id) {
                        echo '<p>No video id passed in</p>';
                        exit;
                    }
                } else {
                    echo '<p>Invalid url</p>';
                    exit;
                }
            }
        } else {
            echo '<p>No video id passed in</p>';
            exit;
        }

        if (isset($_REQUEST['type'])) {
            $my_type = $_REQUEST['type'];
        } else {
            $my_type = 'redirect';
        }

        if ($my_type == 'Download') {
            
        } // end of if for type=Download

        /* First get the video info page for this video id */
//$my_video_info = 'http://www.youtube.com/get_video_info?&video_id='. $my_id;
        $my_video_info = 'http://www.youtube.com/get_video_info?&video_id=' . $my_id . '&asv=3&el=detailpage&hl=en_US'; //video details fix *1
        $my_video_info = curlGet($my_video_info);

        /* TODO: Check return from curl for status code */

        $thumbnail_url = $title = $url_encoded_fmt_stream_map = $type = $url = '';

        parse_str($my_video_info);

        echo '<div id="info">';
        switch ($config['ThumbnailImageMode']) {
            case 2: echo '<img src="getimage.php?videoid=' . $my_id . '" border="0" hspace="2" vspace="2">';
                break;
            case 1: {
                    //Get the file
                    //$content = file_get_contents($thumbnail_url);
//Store in the filesystem.
                    //file_put_contents('packages/default.jpg', file_get_contents($thumbnail_url));
                    /* $fp = fopen("/packages/iamge.jpg", "w");
                      fwrite($fp, $content);
                      fclose($fp); */
                    echo '<img src="' . $thumbnail_url . '" border="0" hspace="2" vspace="2">';
                    break;
                }
            case 0: default:  // nothing
        }
        echo '<p>' . $title . '</p>';
        echo '</div>';
        echo $thumbnail_url;
        $my_title = $title;
        $cleanedtitle = clean($title);

        if (isset($url_encoded_fmt_stream_map)) {
            /* Now get the url_encoded_fmt_stream_map, and explode on comma */
            $my_formats_array = explode(',', $url_encoded_fmt_stream_map);
            if ($debug) {
                echo '<pre>';
                print_r($my_formats_array);
                echo '</pre>';
            }
        } else {
            echo '<p>No encoded format stream found.</p>';
            echo '<p>Here is what we got from YouTube:</p>';
            echo $my_video_info;
        }
        if (count($my_formats_array) == 0) {
            echo '<p>No format stream map found - was the video id correct?</p>';
            exit;
        }

        /* create an array of available download formats */
        $avail_formats[] = '';
        $i = 0;
        $ipbits = $ip = $itag = $sig = $quality = '';
        $expire = time();

        foreach ($my_formats_array as $format) {
            parse_str($format);
            $avail_formats[$i]['itag'] = $itag;
            $avail_formats[$i]['quality'] = $quality;
            $type = explode(';', $type);
            $avail_formats[$i]['type'] = $type[0];
            $avail_formats[$i]['url'] = urldecode($url) . '&signature=' . $sig;
            parse_str(urldecode($url));
            $avail_formats[$i]['expires'] = date("G:i:s T", $expire);
            $avail_formats[$i]['ipbits'] = $ipbits;
            $avail_formats[$i]['ip'] = $ip;
            $i++;
        }

        if ($debug) {
            echo '<p>These links will expire at ' . $avail_formats[0]['expires'] . '</p>';
            echo '<p>The server was at IP address ' . $avail_formats[0]['ip'] . ' which is an ' . $avail_formats[0]['ipbits'] . ' bit IP address. ';
            echo 'Note that when 8 bit IP addresses are used, the download links may fail.</p>';
        }
        if ($my_type == 'Download') {
            echo '<p align="center">List of available formats for download:</p>
		<ul>';

            /* now that we have the array, print the options */
            for ($i = 0; $i < count($avail_formats); $i++) {
                $_GET['token'] = base64_encode($avail_formats[$i]['url']);
                $_GET['mime'] = $avail_formats[$i]['type'];
                $_GET['title'] = urlencode($my_title);
// Check download token
                set_time_limit(0);
                if (empty($_GET['mime']) OR empty($_GET['token'])) {
                    exit('Invalid download token 8{');
                }
// Set operation params
                //Redirect to messageToUser.php
                header("Location: ../");
                //Erase the output buffer
                ob_end_clean();
                //Tell the browser that the connection's closed
                header("Connection: close");

                //Ignore the user's abort (which we caused with the redirect).
                ignore_user_abort(true);

                //Extend time limit to 30 minutes
                set_time_limit(1800);
                //Extend memory limit to 10MB
                ini_set("memory_limit", "10M");
                //Start output buffering again
                ob_start();

                //Tell the browser we're serious... there's really
                //nothing else to receive from this page.
                header("Content-Length: 0");

                //Send the output buffer and turn output buffering off.
                ob_end_flush();
                //Yes... flush again.
                flush();

                //Close the session.
                session_write_close();

                //Do some work
                //Then notify the user that it's finished

                $mime = filter_var($_GET['mime']);
                $ext = str_replace(array('/', 'x-'), '', strstr($mime, '/'));
                $url = base64_decode(filter_var($_GET['token']));
                $name = urldecode($_GET['title']) . '.' . $ext;
// Fetch and serve
// Generate the server headers
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {/*
                  header('Content-Type: "' . $mime . '"');
                  header('Content-Disposition: attachment; filename="' . $name . '"');
                  header('Expires: 0');
                  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                  header("Content-Transfer-Encoding: binary");
                  header('Pragma: public');
                 */
                } else {/*
                  header('Content-Type: "' . $mime . '"');
                  header('Content-Disposition: attachment; filename="' . $name . '"');
                  header("Content-Transfer-Encoding: binary");
                  header('Expires: 0');
                  header('Pragma: no-cache');
                 */
                }
                echo $mime;
                $file_path = 'http://localhost/YoutubeDownloader/' . $name;
                $download_video_file = file_put_contents($name, fopen($url, 'r'), LOCK_EX);
                echo $download_video_file;


// Not found
            }
            echo '</ul><small>Note that you initiate download either by clicking video format link or click "download" to use this server as proxy.</small>';

            if (($config['feature']['browserExtensions'] == true) && (is_chrome()))
                echo '<a href="ytdl.user.js" class="userscript btn btn-mini" title="Install chrome extension to view a \'Download\' link to this application on Youtube video pages."> Install Chrome Extension </a>';
        } else {

            /* In this else, the request didn't come from a form but from something else
             * like an RSS feed.
             * As a result, we just want to return the best format, which depends on what
             * the user provided in the url.
             * If they provided "format=best" we just use the largest.
             * If they provided "format=free" we provide the best non-flash version
             * If they provided "format=ipad" we pull the best MP4 version
             *
             * Thanks to the python based youtube-dl for info on the formats
             *   							http://rg3.github.com/youtube-dl/
             */

            $format = $_REQUEST['format'];
            $target_formats = '';
            switch ($format) {
                case "best":
                    /* largest formats first */
                    $target_formats = array('38', '37', '46', '22', '45', '35', '44', '34', '18', '43', '6', '5', '17', '13');
                    break;
                case "free":
                    /* Here we include WebM but prefer it over FLV */
                    $target_formats = array('38', '46', '37', '45', '22', '44', '35', '43', '34', '18', '6', '5', '17', '13');
                    break;
                case "ipad":
                    /* here we leave out WebM video and FLV - looking for MP4 */
                    $target_formats = array('37', '22', '18', '17');
                    break;
                default:
                    /* If they passed in a number use it */
                    if (is_numeric($format)) {
                        $target_formats[] = $format;
                    } else {
                        $target_formats = array('38', '37', '46', '22', '45', '35', '44', '34', '18', '43', '6', '5', '17', '13');
                    }
                    break;
            }

            /* Now we need to find our best format in the list of available formats */
            $best_format = '';
            for ($i = 0; $i < count($target_formats); $i++) {
                for ($j = 0; $j < count($avail_formats); $j++) {
                    if ($target_formats[$i] == $avail_formats[$j]['itag']) {
                        //echo '<p>Target format found, it is '. $avail_formats[$j]['itag'] .'</p>';
                        $best_format = $j;
                        break 2;
                    }
                }
            }

//echo '<p>Out of loop, best_format is '. $best_format .'</p>';
            if ((isset($best_format)) &&
                    (isset($avail_formats[$best_format]['url'])) &&
                    (isset($avail_formats[$best_format]['type']))
            ) {
                $redirect_url = $avail_formats[$best_format]['url'] . '&title=' . $cleanedtitle;
                $content_type = $avail_formats[$best_format]['type'];
            }
            if (isset($redirect_url)) {
                header("Location: $redirect_url");
            }
        } // end of else for type not being Download
// *1 = thanks to amit kumar @ bloggertale.com for sharing the fix
    }

    public function download() {
        $_GET['token'] = Input::get('token');
        $_GET['mime'] = Input::get('mime');
        $_GET['title'] = Input::get('title');
// Check download token
        set_time_limit(0);
        if (empty($_GET['mime']) OR empty($_GET['token'])) {
            exit('Invalid download token 8{');
        }
// Set operation params
        $mime = filter_var($_GET['mime']);
        $ext = str_replace(array('/', 'x-'), '', strstr($mime, '/'));
        $url = base64_decode(filter_var($_GET['token']));
        $name = urldecode($_GET['title']) . '.' . $ext;
// Fetch and serve
        if ($url) {
// Generate the server headers
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {/*
              header('Content-Type: "' . $mime . '"');
              header('Content-Disposition: attachment; filename="' . $name . '"');
              header('Expires: 0');
              header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
              header("Content-Transfer-Encoding: binary");
              header('Pragma: public');
             */
            } else {/*
              header('Content-Type: "' . $mime . '"');
              header('Content-Disposition: attachment; filename="' . $name . '"');
              header("Content-Transfer-Encoding: binary");
              header('Expires: 0');
              header('Pragma: no-cache');
             */
            }
            echo $mime;
            $file_path = 'http://localhost/YoutubeDownloader/' . $name;
            $download_video_file = file_put_contents($name, fopen($url, 'r'), LOCK_EX);
            echo $download_video_file;
            exit;
        }

// Not found
        exit('File not found 8{');
    }

    public function showVideo() {
        return View::make('video');
    }

}
