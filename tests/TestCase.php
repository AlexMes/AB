<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use InteractsWithAuth;
    use DealsWithSqliteLimitations;

    /**
     * Configure test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Force time for tests, to always be 9:00 AM
        Carbon::setTestNow(now()->hour(9)->minute(0));
        // Force foreign keys for SQLite
        Schema::enableForeignKeyConstraints();
    }
    /**
     * Solution to dropping indexes in SQLite
     *
     * @param string|null $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fixSqlite();
    }

    /**
     * Cleanup tests environment
     *
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
