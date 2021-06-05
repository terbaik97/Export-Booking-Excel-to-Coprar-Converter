<?php

namespace App;

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{

    public function readCell($column, $row, $worksheetName = '')
    {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 1 && $row <= 20)) {
            return true;
        }
        return false;
    }
}
