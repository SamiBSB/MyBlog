<?php

namespace App\Service;

class StringUtils
{
    public function capitalise($string){
        return ucfirst(mb_strtolower($string));
}
}