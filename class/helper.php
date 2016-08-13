<?php

class Helper
{
    function check($msg)
    {

        if (is_array($msg)) {
            foreach ($msg as $key => $val) {
                $msg[$key] = check($val);
            }
        } else {
            $msg = htmlspecialchars($msg);
            $search = array('|', '\'', '$', '\\', '^', '%', '`', "\0", "\x00", "\x1A", chr(226) . chr(128) . chr(174));
            $replace = array('&#124;', '&#39;', '&#36;', '&#92;', '&#94;', '&#37;', '&#96;', '', '', '', '');
            $msg = str_replace($search, $replace, $msg);
            $msg = stripslashes(trim($msg));
        }
        return $msg;
    }

    function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    public function var_dump($data = array())
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public function strip_tags_content($text, $tags = '', $invert = FALSE)
    {

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) AND count($tags) > 0) {
            if ($invert == FALSE) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    public function cleanHtml($str, $space = false)
    {
        $s = preg_replace("/&#?[a-z0-9]{2,8};/i", " ", $str);
        $s = trim($s);
        return $s;
    }

    public function get_web_page($url)
    {
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
        $cookie = realpath('') . '/data/cookie.txt';

        $options = array(

            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POST => false,
            CURLOPT_USERAGENT => $user_agent,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_COOKIEFILE => $cookie,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_MAXREDIRS => 3,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        //$header['errno'] = $err;
        //$header['errmsg'] = $errmsg;
        $header['content'] = $content;
        return $content;
    }

    public function getDayUkr($w)
    {
        $days = array(
            'Неділя', 'Понеділок', 'Вівторок', 'Середа',
            'Четвер', 'П`ятниця', 'Субота'
        );

        $name_day = $days[$w];
        return $name_day;
    }

    public function getMonthUkr($month, $genitive = false, $low = false)
    {
        $trans = array(
            "January" => "Січень",
            "February" => "Лютий",
            "March" => "Березень",
            "April" => "Квітень",
            "May" => "Травень",
            "Jun" => "Червень",
            "Jul" => "Липень",
            "August" => "Серпень",
            "September" => "Вересень",
            "October" => "Жовтень",
            "November" => "Лиспопад",
            "December" => "Грудень",
        );

        $result = strtr($month, $trans);
        if ($low)
            $result = mb_strtolower($result);

        return $result;
    }

    public function group_assoc($array, $key) {
        $return = array();
        foreach($array as $v) {
            $return[$v[$key]][] = $v;
        }
        return $return;
    }


}


