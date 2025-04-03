<?php

use yii\db\Migration;

class m250328_084335_fill_database_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sqlDumpPath = Yii::getAlias('@app/migrations/test_db_data.sql');

        if (!file_exists($sqlDumpPath)) {
            throw new Exception("SQL dump file not found: $sqlDumpPath");
        }

        $handle = fopen($sqlDumpPath, 'r');
        if ($handle === false) {
            throw new Exception("Failed to open SQL dump file: $sqlDumpPath");
        }

        $statement = '';

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
                continue;
            }

            $statement .= ' ' . $line;

            if (substr($line, -1) === ';') {
                $this->execute($statement);
                $statement = '';

                gc_collect_cycles();
            }
        }

        if (trim($statement) !== '') {
            $this->execute($statement);
        }

        fclose($handle);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250328_084335_fill_database_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250328_084335_fill_database_data cannot be reverted.\n";

        return false;
    }
    */
}
