<?php

// Routes

$app->get('/', App\Actions\HomeAction::class)
    ->setName('homepage');

$app->group('/api', function() {
    $this->post('/v1', App\Actions\Api\V1::class);

    $this->post('/v2', App\Actions\Api\V2::class);
});