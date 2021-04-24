<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Sudoku;

class SudokuController extends Controller
{

    public function solve(Request $request)
    {
        $puzzle = $request->puzzle; //input

        $game = new Sudoku();
        $status_dimension = $game->check_dimensions($puzzle);

        // error if dimensions do not match
        if ($status_dimension['status'] == 'error') {
            return response()->json($status_dimension['message']);
        }

        $game->solve_it($puzzle);
        $result = $game->getResult();
        return response()->json($result);
    }
}
