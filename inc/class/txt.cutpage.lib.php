<?php
/*
 *	文章长度分页类
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */


class cutpage{
   public $page = 0;
   public $str = array();
	function __construct($str,$slen){
        $this->page = isset($_GET['ipage'])? (int)$_GET['ipage']:0;
        if ( $this->page >= 1 ){
            $this->page--;
        }
        $len = strlen($str);
        if( $len <= $slen){
            $this->str[] = $str;
            return;
        }

       $i = 0;
       $count = 0;
       $strs = '';
       while ($i < $len){
           $strs .= $str[$i];
           $chr = ord ($str[$i]);
           $count++;
           $i++;
           if ($i >= $len)
               break;
           if ($chr & 0x80){
               $chr <<= 1;
               while ($chr & 0x80) {
                   $strs .= $str[$i];
                   $i++;
                   $chr <<= 1;
               }
           }
           if ( strlen($strs) >= $slen ){
                $this->str[] = $strs;
                $strs = '';
           }
       }
    }

    function get_str(){
        if ( isset($this->str[$this->page]) ){
            return $this->str[$this->page];
        }elseif( isset($this->str[0]) ){
            $this->page = 0;
            return $this->str[0];
        }else{
          $this->page = 0;
           return '';
        }
    }

    function get_page(){
        $this->set_url();
        $sum_page = count($this->str);
        $str = '';
        if ($sum_page>1 and $this->page+1<$sum_page){
            $str .= '<a href="'.$this->url.($this->page+2).'">下页</a>.';
        }
        if ($sum_page>1 and $this->page+1>1){
            $str.= '<a href="'.$this->url.($this->page).'">上页</a>.';
        }
        $str.= '共'.$sum_page.'页,第'.($this->page+1).'页';
        return $str;
    }

    function set_url(){
        parse_str($_SERVER["QUERY_STRING"], $arr_url);
        unset($arr_url["ipage"]);
        if (empty($arr_url)){
            $str = 'ipage=';
        }else{
            $str = http_build_query($arr_url).'&amp;ipage=';
        }
        $this->url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$str;
    }
}
