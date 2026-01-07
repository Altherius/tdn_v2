<?php

use App\Enums\GameResult;
use App\Services\EloRatingService;

beforeEach(function () {
    $this->service = new EloRatingService;
});

test('draw between equal-rated teams exchanges 0 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1000, GameResult::Draw);

    expect($points)->toBe(0);
});

test('win against equal-rated opponent gives 40 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1000, GameResult::Win);

    expect($points)->toBe(40);
});

test('loss against equal-rated opponent loses 40 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1000, GameResult::Loss);

    expect($points)->toBe(-40);
});

test('win against higher-rated opponent gives more than 40 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1200, GameResult::Win);

    expect($points)->toBeGreaterThan(40);
});

test('win against lower-rated opponent gives less than 40 points', function () {
    $points = $this->service->calculatePointsExchange(1200, 1000, GameResult::Win);

    expect($points)->toBeLessThan(40);
});

test('points exchanged are symmetric for win and loss', function () {
    $winPoints = $this->service->calculatePointsExchange(1000, 1000, GameResult::Win);
    $lossPoints = $this->service->calculatePointsExchange(1000, 1000, GameResult::Loss);

    expect($winPoints)->toBe(-$lossPoints);
});

test('higher rating difference leads to more extreme point exchanges', function () {
    $smallDiffWin = $this->service->calculatePointsExchange(1000, 1100, GameResult::Win);
    $largeDiffWin = $this->service->calculatePointsExchange(1000, 1400, GameResult::Win);

    expect($largeDiffWin)->toBeGreaterThan($smallDiffWin);
});
