<?php

use App\Models\User;

test('only admin users are moderators', function () {
    expect((new User(['role' => 'admin']))->isModerator())->toBeTrue()
        ->and((new User(['role' => 'premium']))->isModerator())->toBeTrue()
        ->and((new User(['role' => 'user']))->isModerator())->toBeFalse();
});
