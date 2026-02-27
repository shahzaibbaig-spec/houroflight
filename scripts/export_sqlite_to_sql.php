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

$fileName = 'database_' . date('Ymd_His') . '.sql';
$exportPath = $exportDir . DIRECTORY_SEPARATOR . $fileName;

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fh = fopen($exportPath, 'wb');
if ($fh === false) {
    fwrite(STDERR, "Could not open export file: {$exportPath}\n");
    exit(1);
}

fwrite($fh, "-- SQLite SQL Dump\n");
fwrite($fh, '-- Exported at: ' . date('c') . "\n\n");
fwrite($fh, "PRAGMA foreign_keys=OFF;\n");
fwrite($fh, "BEGIN TRANSACTION;\n\n");

$tablesStmt = $pdo->query(
    "SELECT name, sql FROM sqlite_master
     WHERE type='table'
       AND name NOT LIKE 'sqlite_%'
     ORDER BY name"
);

$tables = $tablesStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tables as $table) {
    $tableName = $table['name'];
    $createSql = $table['sql'];

    if ($createSql !== null && $createSql !== '') {
        fwrite($fh, "DROP TABLE IF EXISTS \"{$tableName}\";\n");
        fwrite($fh, $createSql . ";\n\n");
    }

    $rowsStmt = $pdo->query("SELECT * FROM \"{$tableName}\"");
    $rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rows === []) {
        continue;
    }

    foreach ($rows as $row) {
        $columns = array_map(
            static fn (string $column): string => '"' . str_replace('"', '""', $column) . '"',
            array_keys($row)
        );

        $values = [];
        foreach ($row as $value) {
            if ($value === null) {
                $values[] = 'NULL';
            } elseif (is_int($value) || is_float($value)) {
                $values[] = (string) $value;
            } else {
                $values[] = $pdo->quote((string) $value);
            }
        }

        $insertSql = sprintf(
            "INSERT INTO \"%s\" (%s) VALUES (%s);\n",
            str_replace('"', '""', $tableName),
            implode(', ', $columns),
            implode(', ', $values)
        );
        fwrite($fh, $insertSql);
    }
    fwrite($fh, "\n");
}

fwrite($fh, "COMMIT;\n");
fclose($fh);

echo $exportPath . PHP_EOL;
