<?php

use yii\db\Migration;

class m250328_084335_fill_database_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sqlDumpPath = '@app/migrations/test_db_data.sql';

        $sqlContent = file_get_contents(Yii::getAlias($sqlDumpPath));

        $sqlStatements = explode(';', $sqlContent);

        foreach ($sqlStatements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $this->execute($statement);
            }
        }
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
