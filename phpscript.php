<!DOCTYPE html>
<html>
<head>
    <title>Table</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<br>
<input type="text" id="search" placeholder="search">
<br><br>
<table id="search-list" style="width:80%" bgcolor="000000">
    <tr bgcolor="#aaccdd">
        <th>ID</th>
        <th>Тип</th>
        <th>Връзка</th>
        <th>Мин.Цена</th>
    </tr>

<?php

class RowParser {

    var $row;
    var $row_elem;
    
    // $tag е името на тага $attributes е масив от атрибутите
    function startElement($parser, $tag, $attributes) {
        switch($tag) {
            case 'row':
                $this->row = array('id'=>$attributes['id'], 'price'=>$attributes['price'], 'type'=>$attributes['type'], 'link'=>$attributes['link']);
                break;
            case 'row':
                if ($this->row) {
                    $this->row_elem = $tag;
                }
                break;
        }
    }


    function endElement($parser, $tag) {
        switch($tag) {
            case 'row':
                if ($this->row) {
                    $this->handle_row();
                    $this->row = null;
                }
                break;
            case 'row':
                $this->row_elem = null;
                break;
        }
    }

    
    function cdata($parser, $cdata) {
        if ($this->row && $this->row_elem) {
            $this->row[$this->row_elem] .= $cdata;
        }
    }

    
    function handle_row() {
        $this->print_row();
    }


    function print_row() {

        $country = '';
        $region = '';
        $city = '';
        $hotel = '';
        $color = '';

        if (strpos($this->row['type'], 'country') == 'country') {
            $color = ' bgcolor="#DDEEDD">';
            $link = explode('/', $this->row['link']);
            $country = explode('.', $link[4]);
            $country = $country[0];
        } elseif (strpos($this->row['type'], 'region') == 'region') {
            $color = ' bgcolor="#ffddff">';
            $link = explode('/', $this->row['link']);
            $country = strtoupper($link[4]);
            $region = explode('.', str_replace('_', ' ', $link[5]));
            $region = $region[0].', ';
        } elseif (strpos($this->row['type'], 'city') == 'city') {
            $color = ' bgcolor="#DDFFDD">';
            $link = explode('/', $this->row['link']);
            $country = strtoupper($link[4]);
            $region = '('.str_replace('_', ' ', $link[5]).'), ';
            $city = explode('.', $link[6]);
            $city = $city[0];
        } elseif (strpos($this->row['type'], 'hotel') == 'hotel') {
            $color = ' bgcolor="#ffccff">';
            $link = explode('/', $this->row['link']);
            $country = strtoupper($link[4]);
            $region = str_replace('_', ' ', $link[5]).'), ';
            $city = '('.str_replace('_', ' ', $link[6]).', ';
            $hotel = explode('.', $link[7]);
            $hotel = $hotel[0];
        } 

        echo "<tr><td".$color.$this->row['id']."</td><td".$color.$this->row['type']."</td>"."<td".$color."<a href=".$this->row['link'].">".$hotel.$city.$region.$country."</a></td>"."<td".$color.$this->row['price'].",00&euro;</td></tr>";
    }
}


$xml_handler = new RowParser();
$parser = xml_parser_create();

xml_set_object($parser, $xml_handler);
xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
xml_set_element_handler($parser, "startElement", "endElement");
xml_set_character_data_handler($parser, "cdata");

$fp = fopen('landingpages.xml', 'r');

while ($data = fread($fp, 10000)) {
    xml_parse($parser, $data, feof($fp));
    flush();
}

fclose($fp);
?>
<script type="text/javascript" src="java script/jquery-1.11.2.js"></script>
<script type="text/javascript" src="java script/jquery.js"></script>
</table>
</body>
</html>