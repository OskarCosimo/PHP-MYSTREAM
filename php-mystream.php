/**
 * MYSTREAM
 * @usage:
$filepathlocal = "c:\test.mp4";
$stream = new MYSTREAM($filepathlocal);
$stream->start();
 * created and edited for MYETV.TV
 * file must be protected with proper server-side technology
 */
class MYSTREAM {
//the local path of the environment example c:\..
    private $path = "";
//the default stream name (if available) example test.mp4
    private $stream = "";
    private $buffer = 200000;
    private $start  = -1;
    private $end    = -1;
    private $size   = 0;
 
    function __construct($filePath) 
    {
        $this->path = $filePath;
    } 

    /**
     * Open the stream content
     */
    private function open()
    {
        if (!($this->playMYstream = fopen($this->path, 'rb'))) {
            die('There is some technical problems with this stream');
        }
    }

    /**
     * Set proper header to serve the content
    *header("Cache-Control: max-age=2592000, private");
    *header("Cache-Control: private");
    *header("Cache-Control: no-cache");
     */
    private function setHeader()
    {
        ob_get_clean();
        header("Content-Type: video/mp4");
        header("Cache-Control: private");
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
            fseek($this->playMYstream, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->end/".$this->size);
            session_write_close();
        }
        else
        {
            header("Content-Length: ".$this->size);
            session_write_close();
        }  
    }

    /**
     * close curretly opened stream
     */
    private function end()
    {
        fclose($this->playMYstream);
        exit;
    }

    /**
     * perform the streaming of calculated range
     */
    private function playMYstream()
    {
        $i = $this->start;
        set_time_limit(0);
        while(!feof($this->playMYstream) && $i <= $this->end) {
            $bytesToRead = $this->buffer;
            if(($i+$bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $i + 1;
            }
            $data = fread($this->playMYstream, $bytesToRead);
            echo $data;
            flush();
            $i += $bytesToRead;
        }
    }

    /**
     * Start streaming the content
     */
    function start()
    {
        $this->open();
        $this->setHeader();
        $this->playMYstream();
        $this->end();
    }
}
