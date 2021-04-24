<?php

namespace App\Classes;

class Sudoku
{
    private $puzzle_arr = array();
    private $grids = array();
    private $column_groups = array();

    protected $row_size = 9;
    protected $column_size = 9;

    public function check_dimensions($arr)
    {
        $status = [];

        //check row count
        if (count($arr) < $this->row_size) {
            $status = [
                'status' => 'error',
                'message' => "The number of lines in the puzzle is less than " . $this->row_size,
            ];
            return $status;
        }

        //check column count
        foreach ($arr as  $key => $row) {
            if (count($row) < $this->column_size) {
                $status = [
                    'status' => 'error',
                    'message' => "Line " . ($key + 1) . ": The number of columns in the puzzle is less than " . $this->row_size,
                ];
                return $status;
            }
        }

        $status = [
            'status' => 'success',
            'message' => "",
        ];
        return $status;
    }

    //make data grouping by grid
    private function set_grids_grouping()
    {
        $grids = array();
        foreach ($this->puzzle_arr as $idx_row => $row) {
            if ($idx_row <= 2) {
                $row_num = 1;
            }
            if ($idx_row > 2 && $idx_row <= 5) {
                $row_num = 2;
            }
            if ($idx_row > 5 && $idx_row <= 8) {
                $row_num = 3;
            }

            foreach ($row as $idx_col => $r) {
                if ($idx_col <= 2) {
                    $col_num = 1;
                }
                if ($idx_col > 2 && $idx_col <= 5) {
                    $col_num = 2;
                }
                if ($idx_col > 5 && $idx_col <= 8) {
                    $col_num = 3;
                }
                $grids[$row_num][$col_num][] = $r;
            }
        }
        $this->grids = $grids;
    }

    //make data grouping by column
    private function set_columns_grouping()
    {
        $column_groups = array();
        $i = 1;
        foreach ($this->puzzle_arr as $idx_row => $row) {
            $e = 1;
            foreach ($row as $idx_col => $r) {
                $column_groups[$e][$i] = $r;
                $e++;
            }
            $i++;
        }
        $this->column_groups = $column_groups;
    }

    //get possible values of a coordinates
    private function get_possible_values($idx_row, $idx_col)
    {
        $values = array();
        if ($idx_row <= 2) {
            $row_num = 1;
        }
        if ($idx_row > 2 && $idx_row <= 5) {
            $row_num = 2;
        }
        if ($idx_row > 5 && $idx_row <= 8) {
            $row_num = 3;
        }

        if ($idx_col <= 2) {
            $col_num = 1;
        }
        if ($idx_col > 2 && $idx_col <= 5) {
            $col_num = 2;
        }
        if ($idx_col > 5 && $idx_col <= 8) {
            $col_num = 3;
        }

        //cek nilai apakah belum ada di baris ataupun kolom ataupun grid
        for ($n = 1; $n <= 9; $n++) {
            if (!in_array($n, $this->puzzle_arr[$idx_row]) && !in_array($n, $this->column_groups[$idx_col + 1]) && !in_array($n, $this->grids[$row_num][$col_num])) {
                $values[] = $n;
            }
        }
        shuffle($values);
        return $values;
    }

    public function solve_it($arr)
    {
        while (true) {
            $this->puzzle_arr = $arr;

            $this->set_columns_grouping(); //set pengelompokan by kolom, dimasukkan ke column_groups
            $this->set_grids_grouping(); //set nilai per grid



            $ops = array();
            foreach ($arr as $idx_row => $row) {
                foreach ($row as $idx_col => $r) {
                    if ($r == 0) {
                        $pos_vals = $this->get_possible_values($idx_row, $idx_col); //cek nilai yang mungkin diisikan
                        $ops[] = array(
                            'rowIndex' => $idx_row,
                            'columnIndex' => $idx_col,
                            'possibleValues' => $pos_vals
                        );
                    }
                }
            }



            if (empty($ops)) {
                return $arr;
            }

            usort($ops, array($this, 'sort_by_counts')); //urutkan berdasarkan jumlah possible nya paling sedikit


            //immediately set if the possibility is only 1
            if (count($ops[0]['possibleValues']) == 1) {
                $arr[$ops[0]['rowIndex']][$ops[0]['columnIndex']] = current($ops[0]['possibleValues']);
                continue;
            }

            foreach ($ops[0]['possibleValues'] as $value) {
                $tmp = $arr;
                $tmp[$ops[0]['rowIndex']][$ops[0]['columnIndex']] = $value; // set nilai yg diperbolehkan ke dalam array board sudoku
                if ($this->solve_it($tmp)) { //rekursif set value sampai board terisi semua
                    return $this->solve_it($tmp);
                }
            }

            return false;
        }
    }

    private function sort_by_counts($a, $b)
    {
        $a = count($a['possibleValues']);
        $b = count($b['possibleValues']);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    public function getResult()
    {
        return $this->puzzle_arr;
    }
}
