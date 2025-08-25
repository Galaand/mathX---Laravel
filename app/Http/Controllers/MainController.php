<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    public function home(): View  {
        return view('home');
    }

    public function generateExercises(Request $request)  {

        $request->validate([
            'check_sum' => 'required_without_all:check_subtraction, check_multiplication, check_division',
            'check_subtraction' => 'required_without_all:check_sum, check_multiplication, check_division',
            'check_multiplication' => 'required_without_all:check_sum, check_subtraction, check_division',
            'check_division' => 'required_without_all:check_sum, check_subtraction, check_multiplication',
            'number_one' => 'required|integer|min:0|max:999|lt:number_two',
            'number_two' => 'required|integer|min:0|max:999',
            'number_exercises' => 'required|integer|min:5|max:50',
        ]);

        $operations = [];

        /*
            $operations[] = $request->check_sum ? 'sum' : '';
            $operations[] = $request->check_subtraction ? 'subtraction' : '';
            $operations[] = $request->check_multiplication ? 'multiplication' : '';
            $operations[] = $request->check_division ? 'division' : '';
        */

            $operations = array_filter([
            $request->check_sum ? 'sum' : null,
            $request->check_subtraction ? 'subtraction' : null,
            $request->check_multiplication ? 'multiplication' : null,
            $request->check_division ? 'division' : null,
        ]);

        $min = $request->number_one;
        $max = $request->number_two;

        $numberExercises = $request->number_exercises;

        $exercises = [];
        for($i = 1; $i <= $numberExercises; $i++){

            $operation = $operations[array_rand($operations)];
            $number1 = rand($min, $max);
            $number2 = rand($min, $max);

            $exercise = '';
            $solution = '';

            switch($operation){
                case 'sum':
                    $exercise = "$number1 + $number2 =";
                    $solution =  $number1 + $number2;
                    break;
                case 'subtraction':
                    $exercise = "$number1 - $number2 =";
                    $solution =  $number1 - $number2;
                    break;
                case 'multiplication':
                    $exercise = "$number1 * $number2 =";
                    $solution =  $number1 * $number2;
                    break;
                case 'division':
                    $exercise = "$number1 / $number2 =";
                    $solution =  $number1 / $number2;
                    break;
            }

            $exercises[] = [
                'exercise_number' => $i,
                'exercise' => $exercise,
                'solution' => "$exercise $solution"
            ];


        }


        dd($exercises);

        echo "generateExercises";
    }

    public function printExercises()  {
        echo "printExercises";
    }

    public function exportExercises()  {
        echo "Home";
    }
}
