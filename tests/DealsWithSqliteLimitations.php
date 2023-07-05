<?php

namespace Tests;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\SQLiteBuilder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Fluent;

trait DealsWithSqliteLimitations
{
    /**
     * Fixes problems caused by SQlite which doesn't support
     * dropping foreign keys and forces recreating table
     *
     * @return void
     */
    public function fixSqlite()
    {
        \Illuminate\Database\Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new class($connection, $database, $prefix, $config) extends SQLiteConnection {
                public function getSchemaBuilder()
                {
                    if ($this->schemaGrammar === null) {
                        $this->useDefaultSchemaGrammar();
                    }

                    return new class($this) extends SQLiteBuilder {
                        protected function createBlueprint($table, Closure $callback = null)
                        {
                            return new class($table, $callback) extends Blueprint {
                                public function dropForeign($index)
                                {
                                    return new Fluent();
                                }
                            };
                        }
                    };
                }
            };
        });
    }
}
