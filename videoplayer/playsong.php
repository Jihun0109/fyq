<?php

include("../db_config.php");

if (isset($_GET["filename"])) {
    $filename = $_GET["filename"];
} else {
    $filename = '';
}

if (isset($_COOKIE["member"])) {
	$member_login = $_COOKIE["member"];	
} else {
    $member_login = '';
}

class VideoStream
{
    private $path = "";
    private $stream = "";
    private $buffer = 102400;
    private $start  = -1;
    private $end    = -1;
    private $size   = 0;
 
    function __construct($filePath) 
    {
        $this->path = $filePath;
    }
     
    /**
     * Open stream
     */
    private function open()
    {
        if (!($this->stream = fopen($this->path, 'rb'))) {
            die('Could not open stream for reading');
        }
         
    }
     
    /**
     * Set proper header to serve the video content
     */
    private function setHeader()
    {
        ob_get_clean();
        header("Content-Type: video/mp4");
        header("Cache-Control: max-age=2592000, public");
        header("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
        header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT' );
        $this->start = 0;
        $this->size  = filesize($this->path);
        $this->end   = $this->size - 1;
        header("Accept-Ranges: 0-".$this->end);
         
        if (isset($_SERVER['HTTP_RANGE'])) {
  
            $c_start = $this->start;
            $c_end = $this->end;
 
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            if ($range == '-') {
                $c_start = $this->size - substr($range, 1);
            }else{
                $range = explode('-', $range);
                $c_start = $range[0];
                 
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }
            $c_end = ($c_end > $this->end) ? $this->end : $c_end;
            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            fseek($this->stream, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->end/".$this->size);
        }
        else
        {
            header("Content-Length: ".$this->size);
        }  
         
    }
    
    /**
     * close curretly opened stream
     */
    private function end()
    {
        fclose($this->stream);
        exit;
    }
     
    /**
     * perform the streaming of calculated range
     */
    private function stream()
    {
        $i = $this->start;
        set_time_limit(0);
        while(!feof($this->stream) && $i <= $this->end) {
            $bytesToRead = $this->buffer;
            if(($i+$bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $i + 1;
            }
            $data = fread($this->stream, $bytesToRead);
            echo $data;
            flush();
            $i += $bytesToRead;
        }
    }
     
    /**
     * Start streaming video content
     */
    function start()
    {
        $this->open();
        $this->setHeader();
        $this->stream();
        $this->end();
    }
}

$allowedUser = true;
//echo $tid;
//echo $member_login;
// if ($tid !== '' and $member_login !== ''){
//     $query = "SELECT count(*) FROM payment_list WHERE pay_member = '{$member_login}' and pay_shop = {$tid} and pay_status = 1;";
    
//     $result = mysqli_query($mysqli, $query);
//     if ($result){
//         $paid = mysqli_fetch_array($result, MYSQLI_NUM);
//         if ($paid[0]>0)
//             $allowedUser = true;
//         //echo $query;
//     }
//     else{
//         //echo "Query Error";
//     }
// }

// if (!$allowedUser){ // If this media 's owner is me.
//     $query1 = "SELECT count(*) FROM teacher_list WHERE tl_id = '{$tid}' and tl_phone = '{$member_login}'" ;
//     $result1 = mysqli_query($mysqli, $query1);
    
//     if ($result1){
//         $owner = mysqli_fetch_array($result1, MYSQLI_NUM);
//         if ($owner[0]>0)
//             $allowedUser = true;
//         //echo $query1;
//     }
//     else{
//         echo "Query1 Error";
//     }

// }

if ($allowedUser){

    // $query2 = "SELECT * FROM teacher_list WHERE tl_id = {$tid};";
    // $result2 = mysqli_query($mysqli, $query2);
    $filepath = "../uploads/";
    $filepath = $filepath.end(explode('.', $filename));
    $filepath = $filepath."/";
    $filepath = $filepath.$filename;

    //if ($result2){
        //$row = mysqli_fetch_assoc($result2);
        
        $stream = new VideoStream($filepath);
        $stream->start();
    //}
    //else{
    //    echo "Something went wrong!!";
    //}    
}
else{
    //echo "ERROR!";
}

?>