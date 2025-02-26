<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function class_grouper($class_names) {
        $grouped = all_groups();

        foreach ($class_names as $key => $value) {
            $grouped[groupName($value)][] = $key;
        }

        $groupedNotEmpty = array();

        foreach ($grouped as $key => $value) {
            if (count($value) != 0) {
                $groupedNotEmpty[$key] = $value;
            }
        }

        return $groupedNotEmpty;
    }
}
