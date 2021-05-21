<?php

use \yii\db\Migration;

class m190124_110200_add_verification_token_column_to_user_table extends Migration
{
    public function up()
    {
<<<<<<< HEAD
        $this->addColumn('user', 'verification_token', $this->string()->defaultValue(null));
        
=======
        $this->addColumn('{{%user}}', 'verification_token', $this->string()->defaultValue(null));
>>>>>>> a3c1c4c800d00268a760adcec036b45a0d027643
    }

    public function down()
    {
<<<<<<< HEAD
        $this->dropColumn('user', 'verification_token');
=======
        $this->dropColumn('{{%user}}', 'verification_token');
>>>>>>> a3c1c4c800d00268a760adcec036b45a0d027643
    }
}
