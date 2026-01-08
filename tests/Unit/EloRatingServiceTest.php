<?php

use App\Enums\GameResult;
use App\Services\EloRatingService;

beforeEach(function () {
    $this->service = new EloRatingService;
});

test('draw between equal-rated teams exchanges 0 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1000, GameResult::Draw, 0);

    expect($points)->toBe(0);
});

test('win against equal-rated opponent gives 20 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1000, GameResult::Win, 0);

    expect($points)->toBe(20);
});

test('loss against equal-rated opponent loses 20 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1000, GameResult::Loss, 0);

    expect($points)->toBe(-20);
});

test('win against higher-rated opponent gives more than 20 points', function () {
    $points = $this->service->calculatePointsExchange(1000, 1200, GameResult::Win, 0);

    expect($points)->toBeGreaterThan(20);
});

test('win against lower-rated opponent gives less than 20 points', function () {
    $points = $this->service->calculatePointsExchange(1200, 1000, GameResult::Win, 0);

    expect($points)->toBeLessThan(20);
});

test('points exchanged are symmetric for win and loss', function () {
    $winPoints = $this->service->calculatePointsExchange(1000, 1000, GameResult::Win, 0);
    $lossPoints = $this->service->calculatePointsExchange(1000, 1000, GameResult::Loss, 0);

    expect($winPoints)->toBe(-$lossPoints);
});

test('higher rating difference leads to more extreme point exchanges', function () {
    $smallDiffWin = $this->service->calculatePointsExchange(1000, 1100, GameResult::Win, 0);
    $largeDiffWin = $this->service->calculatePointsExchange(1000, 1400, GameResult::Win, 0);

    expect($largeDiffWin)->toBeGreaterThan($smallDiffWin);
});
