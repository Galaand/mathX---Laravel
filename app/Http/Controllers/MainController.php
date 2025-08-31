<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function generateExercises(Request $request): View
    {

        $request->validate([
            'check_sum' => 'required_without_all:check_subtraction,check_multiplication,check_division',
            'check_subtraction' => 'required_without_all:check_sum,check_multiplication,check_division',
            'check_multiplication' => 'required_without_all:check_sum,check_subtraction,check_division',
            'check_division' => 'required_without_all:check_sum,check_subtraction,check_multiplication',
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
        for ($i = 1; $i <= $numberExercises; $i++) {
            $exercises[] = $this->generateExercise($i, $operations, $min, $max);
        }

        $request->session()->put('exercises',$exercises);
        //oder
        //session(['exercises' => $exercises]);

        return view('operations', ['exercises' => $exercises]);
    }

    public function printExercises()
    {
       if(!session()->has('exercises'))
        return redirect()->route('home');

       $exercises = session('exercises');

       echo '<pre>';
       echo '<h1> Exercicios de Matemática (' . env('APP_NAME') . ') </h1>';
       echo '<hr>';

        foreach($exercises as $exercise){
            echo '<h2><small>' . $exercise['exercise_number'] . ' >> </small>' . $exercise['exercise'] . '</h2>';
        }
        //sollutions
        echo '<hr>';
        echo '<small> Soluções: </small><br>';
        foreach($exercises as $exercise){
            echo '<small>' . $exercise['exercise_number'] . ' >> ' . $exercise['sollution'] . '</small><br>';
        }
    }

    public function exportExercises()
    {
        // check
        if(!session()->has('exercises'))
            return redirect()->route('home');

        $exercises = session('exercises');

        $filename = 'exercises_' . env('APP_NAME') . '_' . date('YmdHis') . '.txt';

        $content = '';
        foreach ($exercises as $exercise){
            $content .= $exercise['exercise_number'] . ') ' . $exercise['exercise'] . "\n";
        }

        $content .= "\n";
        $content .= "Soluções\n";
        foreach ($exercises as $exercise){
            $content .= $exercise['exercise_number'] . ' >> ' . $exercise['sollution'] . "\n";
        }

        return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');


    }
    private function generateExercise($index, $operations, $min, $max): array
    {
        $operation = $operations[array_rand($operations)];

        $number1 = rand($min, $max);
        $number2 = rand($min, $max);

        $exercise = '';
        $solution = '';

        switch ($operation) {
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

                $number2 = $number2 == 0 ? 1 : $number2;

                $exercise = "$number1 / $number2 =";
                $solution =  $number1 / $number2;
                break;
        }

        $solution = round($solution, 2);

        return [
            'operation'       => $operation,
            'exercise_number' => str_pad($index, 2, "0", STR_PAD_LEFT),
            'exercise'        => $exercise,
            'sollution'       => $solution
        ];
    }
}
