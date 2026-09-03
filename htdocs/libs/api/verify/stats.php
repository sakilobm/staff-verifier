<?php
use App\College;

$stats = function () {
    $data = College::getOverallStats();
    $this->response($this->json($data), 200);
};
