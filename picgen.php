<?php
    ini_set('display_errors', "On");
    define("FONT_SIZE", 45);
    define("FONT_LINE_SPACING", 50);
    define("FONT_DARKNESS", 0);
    define("FONT_OFFSET", 9);
    define("PAGE_PER_LINES", 17);
    define("PAGE_HEIGHT", 2894);
    define("PAGE_WIDTH", 2039);
    define("PAGE_HEADER", 350);
    define("PAGE_FOOTER", 300);
    define("PAGE_START", 120);

    define("FONT_PATH", "./fonts/ipaexm_rev.ttf");
    define("TEXT_FILE_ROOT", "./backnumbers/text/");


    $mid = $_GET["mid"];
    $cid = $_GET["cid"];
    $cnum = $_GET["cnum"];
    $schar = $_GET["schar"];
    $page = $_GET["page"];

    $img = imagecreate(PAGE_WIDTH, PAGE_HEIGHT);
    imageantialias ( $img , false );

    $white = imagecolorallocate($img, 255, 255, 255);
    $gray = imagecolorallocate($img, FONT_DARKNESS, FONT_DARKNESS, FONT_DARKNESS);

    $font = FONT_PATH;

    $url = TEXT_FILE_ROOT . $cid."_".$mid."_".$cnum.".txt";

    $text = "";
    try{
        $text = file_get_contents($url);
    }catch(Exception $e){
        $text = "　この作品は閲覧できません。";
    }
    
    $text = mb_substr($text, 1);
    $l = mb_strlen($text,'UTF-8');
    $chunked = array();
    for ($i=0; $i<$l; $i++) {
        $chunked[] = mb_substr($text,$i,1,'UTF-8');
    }

    $chunks = count($chunked);
    if($schar > $chunks)$schar = 0;
    for($i = $schar; $i < $chunks -1; $i++){
        if(RenderCharactor($chunked[$i], $chunked[$i+1]) == 1)break;
    }

    RenderPagenumber($page);

    header('Content-Type: image/png');

    imagepng($img);

    function RenderCharactor($char, $nextChar){
        static $line = 0;
        static $prevCharX = PAGE_START;
        static $height = 0;
        static $bboxHeight = PAGE_HEIGHT - PAGE_HEADER - PAGE_FOOTER;
        global $white, $gray, $font, $img;
        static $charX = 0;

        $charX = $prevCharX + FONT_SIZE + FONT_LINE_SPACING;
        $charY = $height + PAGE_HEADER;
        imagettftext($img, FONT_SIZE, 0, PAGE_WIDTH - $charX - FONT_SIZE, $charY, $gray, $font, $char);
        $height += FONT_SIZE + FONT_OFFSET;

        if($height >= $bboxHeight && strcmp($nextChar, "。") != 0 && strcmp($nextChar, "、") != 0){
            $line++;
            $height = 0;
            $prevCharX = $charX;
        }

        if(strcmp($char, "\n") == 0){
            $line++;
            $height = 0;
            $prevCharX = $charX;
        }

        if($line > PAGE_PER_LINES){
            return 1;
        }else{
            return 0;
        }
    }

    function RenderPagenumber($num){
        global $white, $gray, $font, $img;
        imagettftext($img, FONT_SIZE, 0, PAGE_WIDTH/2, PAGE_HEIGHT -(PAGE_FOOTER /2), $gray, $font, $num);
    }
?>
