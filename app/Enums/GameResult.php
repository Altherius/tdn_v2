<?php

namespace App\Enums;

enum GameResult: string
{
    case Win = 'win';
    case Draw = 'draw';
    case Loss = 'loss';
}
