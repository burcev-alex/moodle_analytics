<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Storage;

class LsaRecommendController extends Controller
{
    public function download()
    {
        $courseName = "Меиа";
        $quizId = 11111;
        $dateCreate = date('d.m.Y');
        
        $questions = [
            [
                'name' => 'Вопрос №1',
                'id' => 12222,
                'pages' => 
                [
                    [
                        'title' => 'Конспект №1', 
                        'id' => 123, 
                        'link' => 'http://8.8.8.8/ping.php'
                    ],
                    [
                        'title' => 'Конспект №2', 
                        'id' => 435, 
                        'link' => 'http://8.8.8.8/ping.php'
                    ],
                ]
            ],
            [
                'name' => 'Вопрос №12',
                'id' => 433,
                'pages' =>
                [
                    [
                        'title' => 'Конспект №3', 
                        'id' => 123, 
                        'link' => 'http://91.200.42.232/mod/page/view.php?id=190'
                    ],
                    [
                        'title' => 'Конспект №4', 
                        'id' => 435, 
                        'link' => 'http://91.200.42.232/mod/page/view.php?id=190'
                    ],
                ]
            ]
        ];

        $pdf = PDF::loadView('pdf.lsarecommend', compact('questions', 'courseName', 'quizId', 'dateCreate'));

        $filename = md5(rand(1, 100000000));
        Storage::put('public/pdf/'.$filename.'.pdf', $pdf->output());
        return config('app.url').'/storage/pdf/'.$filename.'.pdf';
    }
}
