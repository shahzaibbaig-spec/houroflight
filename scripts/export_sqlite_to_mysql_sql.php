<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dbPath = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
$exportDir = $root . DIRECTORY_SEPARATOR . 'exports';

if (! file_exists($dbPath)) {
    fwrite(STDERR, "Database file not found: {$dbPath}\n");
    exit(1);
}

if (! is_dir($exportDir) && ! mkdir($exportDir, 0777, true) && ! is_dir($exportDir)) {
    fwrite(STDERR, "Could not create export directory: {$exportDir}\n");
    exit(1);
}

function qIdent(string $name): string
{
    return '`' . str_replace('`', '``', $name) . '`';
}

function qVal(mixed $value): string
{
    if ($value === null) {
        return 'NULL';
    }

    if (is_bool($value)) {
        return $value ? '1' : '0';
    }

    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }

    return "'" . str_replace("'", "''", (string) $value) . "'";
}

$fileName = 'database_mysql_' . date('Ymd_His') . '.sql';
$exportPath = $exportDir . DIRECTORY_SEPARATOR . $fileName;

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fh = fopen($exportPath, 'wb');
if ($fh === false) {
    fwrite(STDERR, "Could not open export file: {$exportPath}\n");
    exit(1);
}

fwrite($fh, "-- MySQL/MariaDB compatible data dump generated from SQLite\n");
fwrite($fh, '-- Exported at: ' . date('c') . "\n\n");
fwrite($fh, "SET NAMES utf8mb4;\n");
fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($fh, "START TRANSACTION;\n\n");

$tablesStmt = $pdo->query(
    "SELECT name FROM sqlite_master
     WHERE type='table'
       AND name NOT LIKE 'sqlite_%'
     ORDER BY name"
);

$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $tableName) {
    $tableName = (string) $tableName;
    $qTable = qIdent($tableName);

    fwrite($fh, "-- Table: {$tableName}\n");
    fwrite($fh, "DELETE FROM {$qTable};\n");

    $rowsStmt = $pdo->query('SELECT * FROM "' . str_replace('"', '""', $tableName) . '"');
    $rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows === []) {
        fwrite($fh, "\n");
        continue;
    }

    $columns = array_keys($rows[0]);
    $qColumns = array_map(
        static fn (string $col): string => qIdent($col),
        $columns
    );
    $columnList = implode(', ', $qColumns);

    foreach ($rows as $row) {
        $values = [];
        foreach ($columns as $col) {
            $values[] = qVal($row[$col] ?? null);
        }

        fwrite(
            $fh,
            "INSERT INTO {$qTable} ({$columnList}) VALUES (" . implode(', ', $values) . ");\n"
        );
    }

    fwrite($fh, "\n");
}

fwrite($fh, "COMMIT;\n");
fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($fh);

echo $exportPath . PHP_EOL;
