<?php
require_once('libs/helper.php');
require_once('./libs/bootstrap.php');
class Migration
{
    private $db;
    public function __construct()
    {
        $this->db = DB::connect();
        if (!file_exists('./tmp')) mkdir('./tmp');
        foreach(glob('./tmp/*.sql') as $file) unlink($file);
        $this->createCurrentTableSchema();
        $this->createOldTableSchema();
        exec('./bin/schemalex -o ./tmp/migration.sql ./tmp/old_schema.sql ./tmp/current_schema.sql');
        $sql = file_get_contents('./tmp/migration.sql');
        echo "execute sql\n {$sql}\n";
        $this->db->execute($sql);

        exec('rm -r tmp');
    }

    private function createCurrentTableSchema()
    {
        $files = glob("./db/*.sql");
        foreach ($files as $file) {
            file_put_contents('./tmp/current_schema.sql', file_get_contents($file).";\n", FILE_APPEND|FILE_IGNORE_NEW_LINES);
        }
    }

    private function createOldTableSchema()
    {
        $tables = $this->db->getTables();
        foreach ($tables as $table) {
            $schema = $this->db->execute("SHOW CREATE TABLE $table");
            if (!$schema) continue;
            file_put_contents('./tmp/old_schema.sql', $schema[0]['Create Table'].";\n", FILE_APPEND);
        }
    }
}

new Migration();
