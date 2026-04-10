<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    /**
     * Download a full SQL dump of the database.
     * Only accessible by admin (enforced via route middleware).
     */
    public function download(): StreamedResponse
    {
        $dbName   = config('database.connections.mysql.database');
        $filename = 'ent-hns-backup-' . now()->format('Y-m-d_His') . '.sql';

        ActivityLog::log(
            action:      'database.backup',
            description: "Admin downloaded a full database backup ({$filename})",
            severity:    'warning',
        );

        return response()->streamDownload(function () use ($dbName) {
            echo $this->generateSqlDump($dbName);
        }, $filename, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Private: build the SQL dump string
    // ─────────────────────────────────────────────────────────────

    private function generateSqlDump(string $dbName): string
    {
        $sql  = "-- ============================================================\n";
        $sql .= "-- ENT-HNS Database Backup\n";
        $sql .= "-- Generated : " . now()->toDateTimeString() . "\n";
        $sql .= "-- Database  : {$dbName}\n";
        $sql .= "-- ============================================================\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $dbName;

        foreach ($tables as $tableRow) {
            $table = $tableRow->$tableKey;

            // ── DROP + CREATE TABLE ──────────────────────────────
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "-- Table: `{$table}`\n";
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
            $sql .= $createRow[0]->{'Create Table'} . ";\n\n";

            // ── DATA ─────────────────────────────────────────────
            $rows = DB::table($table)->get();

            if ($rows->isEmpty()) {
                $sql .= "-- (no rows)\n\n";
                continue;
            }

            $columns = array_keys((array) $rows->first());
            $colList = '`' . implode('`, `', $columns) . '`';

            $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";

            $rowStrings = $rows->map(function ($row) {
                $values = array_map(function ($value) {
                    if ($value === null) return 'NULL';
                    if (is_numeric($value)) return $value;
                    return "'" . addslashes((string) $value) . "'";
                }, (array) $row);

                return '(' . implode(', ', $values) . ')';
            });

            $sql .= $rowStrings->implode(",\n") . ";\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "-- ============================================================\n";
        $sql .= "-- End of backup\n";
        $sql .= "-- ============================================================\n";

        return $sql;
    }
}