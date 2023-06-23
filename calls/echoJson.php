<?php

namespace includes;

class echoJson
{

    public function echoJSON($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }
}