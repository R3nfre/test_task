<?php

use yii\db\Migration;

class m250328_082318_create_db_structure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sqlDumpPath = '@app/migrations/test_db_structure.sql';

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
        echo "m250328_082318_create_db_structure cannot be reverted.\n";

        return false;
    }
}
