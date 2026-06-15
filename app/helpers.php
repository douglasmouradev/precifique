<?php

declare(strict_types=1);
use Illuminate\Support\Facades\DB;
use Pdo\Mysql;

/**
 * Opções PDO MySQL para SSL CA (compatível PHP 8.5+ e versões anteriores).
 *
 * @return array<int, mixed>
 */
function mysql_pdo_ssl_options(): array
{
    if (! extension_loaded('pdo_mysql')) {
        return [];
    }

    $sslCa = env('MYSQL_ATTR_SSL_CA');

    if ($sslCa === null || $sslCa === '') {
        return [];
    }

    if (class_exists(Mysql::class)) {
        return [Mysql::ATTR_SSL_CA => $sslCa];
    }

    return [PDO::MYSQL_ATTR_SSL_CA => $sslCa];
}

/**
 * Expressão SQL para extrair o ano de uma coluna datetime (SQLite / MySQL / PostgreSQL).
 */
function sql_year(string $column): string
{
    return match (DB::connection()->getDriverName()) {
        'sqlite' => "CAST(strftime('%Y', {$column}) AS INTEGER)",
        'pgsql' => "EXTRACT(YEAR FROM {$column})::INTEGER",
        default => "YEAR({$column})",
    };
}

/**
 * Expressão SQL para extrair o mês de uma coluna datetime (SQLite / MySQL / PostgreSQL).
 */
function sql_month(string $column): string
{
    return match (DB::connection()->getDriverName()) {
        'sqlite' => "CAST(strftime('%m', {$column}) AS INTEGER)",
        'pgsql' => "EXTRACT(MONTH FROM {$column})::INTEGER",
        default => "MONTH({$column})",
    };
}
