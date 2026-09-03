<?php
use App\Verification;

$reset_all = function () {
    $success = Verification::resetAll();
    $this->response($this->json([
        'status'  => $success ? 'success' : 'error',
        'message' => 'All verification records have been reset',
    ]), $success ? 200 : 500);
};
