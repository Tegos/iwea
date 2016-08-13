<?php


class File
{
    private $expire;

    public function __construct($expire = 0)
    {
        if ($expire < 1) $this->expire = 24 * 3 * 3600;
        $this->expire = $expire;

        $files = glob(DIR_CACHE . 'cache.*');

        if ($files) {
            foreach ($files as $file) {
                $time = substr(strrchr($file, '.'), 1);

                if ($time < time()) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }


    public function get($key)
    {
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');
        if ($files) {
            $handle = fopen($files[0], 'r');
            flock($handle, LOCK_SH);
            $data = fread($handle, filesize($files[0]));
            flock($handle, LOCK_UN);
            fclose($handle);
            return unserialize($data);
        }
        return false;
    }

    public function set($key, $value)
    {
        $this->delete($key);
        $file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $this->expire);
        $handle = fopen($file, 'w');
        flock($handle, LOCK_EX);
        fwrite($handle, serialize($value));
        unset($value);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public function delete($key)
    {
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    public function write($path, $value)
    {
        $file = $path;
        $handle = fopen($file, 'w');
        flock($handle, LOCK_EX);
        fwrite($handle, $value);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public function getFile($key)
    {
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');
        if ($files) {
            return $files[0];
        }
        return false;
    }

}


function do_callback($element)
{
    if ($element->tag == 'a') {
        $href = $element->href;
        if ($href[0] != '#')
            $element->href = '/?path=' . $href;
    }
    /*
    if ($element->tag == 'img') {
        $src = $element->src;
        $element->src = 'http://www.vsetv.com/' . $src;
    }
    */
    if ($element->class == 'chnum') {
        $element->outertext = '';
    }
    if ($element->tag == 'td') {
        if ($element->align == 'right')
            $element->outertext = '';
    }
    if ($element->tag == 'img') {
        if ($element->src == 'pic/ico_comm.gif')
            $element->outertext = '';
    }
    if ($element->tag == 'a') {
        if (strpos($element->href, '#comments') !== false || $element->href == '/tele/?path=register.php')
            $element->outertext = '';
        if ($element->class == 'b') {
            $element->class = '';
        }
        if (strpos($element->href, 'support@vsetv.com') !== false) {
            $element->innertext = 'mihavko_ivan@mail.ru';
            $element->href = 'mailto:mihavko_ivan@mail.ru';
        }

    }
    if ($element->tag == 'div') {
        if ($element->style == 'width:300px') {
            $element->style = 'width:370px';
        }
    }
}