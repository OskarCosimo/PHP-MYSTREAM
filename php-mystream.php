/**
 * MYSTREAM
 * @usage: MYSTREAM($localpath)
 * created and edited for MYETV.TV
 * file must be protected with proper server-side technology, use slash or backslash regarding the environment
 */
class MYSTREAM {
    private $path = "";
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
