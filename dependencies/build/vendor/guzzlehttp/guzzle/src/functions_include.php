<?php

namespace QuillSMTP\Vendor;

// Don't redefine the functions if included multiple times.
if (!\function_exists('QuillSMTP\\Vendor\\GuzzleHttp\\describe_type')) {
    require __DIR__ . '/functions.php';
}
