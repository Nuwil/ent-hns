<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // EXPORT — stream SQL dump directly to browser (memory-safe)
    // ──────────────────────────────────────────────────────────────

    public function download(): StreamedResponse
    {
        $dbName   = config('database.connections.mysql.database');
        $filename = 'ent-hns-backup-' . now()->format('Y-m-d_His') . '.sql';

        ActivityLog::log(
            action:      'database.backup',
            description: "Admin downloaded a full database backup ({$filename})",
            severity:    'warning',
        );

        return response()->stream(function () use ($dbName) {
            $this->streamSqlDump($dbName);
        }, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering'   => 'no',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // IMPORT — execute an uploaded .sql file against the database
    // ──────────────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => [
                'required',
                'file',
                'mimes:sql,txt',
                'max:20480',
            ],
        ]);

        $file    = $request->file('sql_file');
        $content = file_get_contents($file->getRealPath());

        if (empty(trim($content))) {
            return back()->with('toast_error', 'The uploaded file is empty.');
        }

        // Block dangerous statements that have no place in a restore file
        $forbidden = [
            '/\bDROP\s+DATABASE\b/i',
            '/\bCREATE\s+DATABASE\b/i',
            '/\bGRANT\b/i',
            '/\bREVOKE\b/i',
            '/\bSHUTDOWN\b/i',
            '/\bLOAD\s+DATA\b/i',
            '/\bINTO\s+OUTFILE\b/i',
            '/\bINTO\s+DUMPFILE\b/i',
        ];

        foreach ($forbidden as $pattern) {
            if (preg_match($pattern, $content)) {
                ActivityLog::log(
                    action:      'database.import_blocked',
                    description: 'SQL import blocked — file contained forbidden statements.',
                    severity:    'danger',
                );
                return back()->with('toast_error', 'Import blocked: the file contains forbidden SQL statements (e.g. DROP DATABASE, GRANT, LOAD DATA).');
            }
        }

        $statements = $this->parseSqlStatements($content);

        if (empty($statements)) {
            return back()->with('toast_error', 'No valid SQL statements found in the file.');
        }

        DB::beginTransaction();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($statements as $stmt) {
                DB::statement($stmt);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            ActivityLog::log(
                action:      'database.import_failed',
                description: 'SQL import failed: ' . $e->getMessage(),
                severity:    'danger',
            );

            return back()->with('toast_error', 'Import failed: ' . $e->getMessage());
        }

        ActivityLog::log(
            action:      'database.import',
            description: "Admin imported SQL file: {$file->getClientOriginalName()} ({$this->formatBytes($file->getSize())})",
            severity:    'warning',
        );

        return back()->with('toast_success', 'Database imported successfully (' . count($statements) . ' statements executed).');
    }

    // ──────────────────────────────────────────────────────────────
    // Private: stream the SQL dump table-by-table (no memory spike)
    // ──────────────────────────────────────────────────────────────

    private function streamSqlDump(string $dbName): void
    {
        $this->out("-- ============================================================\n");
        $this->out("-- ENT-HNS Database Backup\n");
        $this->out("-- Generated : " . now()->toDateTimeString() . "\n");
        $this->out("-- Database  : {$dbName}\n");
        $this->out("-- ============================================================\n\n");
        $this->out("SET FOREIGN_KEY_CHECKS=0;\n");
        $this->out("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
        $this->out("SET NAMES utf8mb4;\n\n");

        $tableKey = 'Tables_in_' . $dbName;
        $tables   = DB::select('SHOW TABLES');

        foreach ($tables as $tableRow) {
            $table = $tableRow->$tableKey;

            $this->out("-- ------------------------------------------------------------\n");
            $this->out("-- Table: `{$table}`\n");
            $this->out("-- ------------------------------------------------------------\n");
            $this->out("DROP TABLE IF EXISTS `{$table}`;\n");

            $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
            $this->out($createRow[0]->{'Create Table'} . ";\n\n");

            $hasRows = false;
            $columns = null;

            DB::table($table)->orderBy(DB::raw('1'))->chunk(200, function ($rows) use ($table, &$hasRows, &$columns) {
                if ($rows->isEmpty()) return;

                if (!$hasRows) {
                    $columns = array_keys((array) $rows->first());
                    $hasRows = true;
                }

                $colList = '`' . implode('`, `', $columns) . '`';

                $rowStrings = $rows->map(function ($row) {
                    $values = array_map(function ($value) {
                        if ($value === null)    return 'NULL';
                        if (is_numeric($value)) return $value;
                        return "'" . addslashes((string) $value) . "'";
                    }, (array) $row);

                    return '(' . implode(', ', $values) . ')';
                });

                $this->out("INSERT INTO `{$table}` ({$colList}) VALUES\n");
                $this->out($rowStrings->implode(",\n") . ";\n\n");
            });

            if (!$hasRows) {
                $this->out("-- (no rows)\n\n");
            }

            flush();
        }

        $this->out("SET FOREIGN_KEY_CHECKS=1;\n");
        $this->out("-- ============================================================\n");
        $this->out("-- End of backup\n");
        $this->out("-- ============================================================\n");
    }

    // ──────────────────────────────────────────────────────────────
    // Private: flush output immediately (memory-safe streaming)
    // ──────────────────────────────────────────────────────────────

    private function out(string $text): void
    {
        echo $text;
        if (ob_get_level() > 0) ob_flush();
        flush();
    }

    // ──────────────────────────────────────────────────────────────
    // Private: split SQL into individual statements safely
    // Handles comments, block comments, and quoted strings
    // so semicolons inside them are never treated as delimiters.
    // ──────────────────────────────────────────────────────────────

    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $inString   = false;
        $stringChar = '';
        $len        = strlen($sql);
        $i          = 0;

        while ($i < $len) {
            $char = $sql[$i];

            // Block comment /* ... */
            if (!$inString && $char === '/' && isset($sql[$i + 1]) && $sql[$i + 1] === '*') {
                $end = strpos($sql, '*/', $i + 2);
                $i   = $end !== false ? $end + 2 : $len;
                continue;
            }

            // Single-line comment --
            if (!$inString && $char === '-' && isset($sql[$i + 1]) && $sql[$i + 1] === '-') {
                $end = strpos($sql, "\n", $i);
                $i   = $end !== false ? $end + 1 : $len;
                continue;
            }

            // String open
            if (!$inString && ($char === "'" || $char === '"' || $char === '`')) {
                $inString   = true;
                $stringChar = $char;
                $current   .= $char;
                $i++;
                continue;
            }

            if ($inString) {
                // Escaped character inside string
                if ($char === '\\' && isset($sql[$i + 1])) {
                    $current .= $char . $sql[$i + 1];
                    $i       += 2;
                    continue;
                }
                if ($char === $stringChar) {
                    $inString = false;
                }
                $current .= $char;
                $i++;
                continue;
            }

            // Statement terminator
            if ($char === ';') {
                $stmt = trim($current);
                if ($stmt !== '') {
                    $statements[] = $stmt;
                }
                $current = '';
                $i++;
                continue;
            }

            $current .= $char;
            $i++;
        }

        // Trailing statement with no semicolon
        $stmt = trim($current);
        if ($stmt !== '') {
            $statements[] = $stmt;
        }

        return $statements;
    }

    // ──────────────────────────────────────────────────────────────
    // Private: human-readable file size
    // ──────────────────────────────────────────────────────────────

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 2) . ' MB';
        if ($bytes >= 1_024)     return round($bytes / 1_024, 2) . ' KB';
        return $bytes . ' B';
    }
}