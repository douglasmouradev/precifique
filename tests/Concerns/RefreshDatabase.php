<?php

declare(strict_types=1);

namespace Tests\Concerns;

trait RefreshDatabase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase {
        migrateFreshUsing as parentMigrateFreshUsing;
    }

    protected function migrateFreshUsing(): array
    {
        return array_merge($this->parentMigrateFreshUsing(), ['--force' => true]);
    }
}
