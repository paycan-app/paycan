<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent SQLite VACUUM operations during tests
        if (config('database.default') === 'sqlite') {
            \DB::unprepared('PRAGMA auto_vacuum = NONE');
            \DB::unprepared('PRAGMA journal_mode = MEMORY');
        }
    }
}
