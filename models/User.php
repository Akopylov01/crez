<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $patronymic
 * @property string $login
 * @property string $email
 * @property string $password
 * @property int $role
 * @property int|null $rule
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    /**
     * {@inheritdoc}
     */

    public $password2;
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'patronymic', 'login', 'email', 'password'], 'required'],
            ['login', 'validateLogin'],
            ['email', 'email'],
            ['password2', 'compare', 'compareAttribute' => 'password'],
            [['role', 'rule'], 'integer'],
            [['name', 'surname', 'patronymic', 'login', 'email', 'password'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'login' => 'Логин',
            'email' => 'Почта',
            'password' => 'Пароль',
            'password2' => 'Повторите пароль',

            'role' => 'Role',
            'rule' => 'Согласен с правилами сервиса',
        ];
    }
    public function validatePassword($password){
        return $this->password === md5($password);
    }
    static public function findByUsername($username){
        return self::find()->where(['login'=>$username])->one();
    }
    public function validateLogin($attribute){
        $user = self::find()->where(['login'=>$this->login])->one();

        if($user !== null){
            $this->addError($attribute, 'Логин занят');

        }
    }
    public function beforeSave($insert)
    {
        $this->password = md5($this->password);
        return true;
    }
    public function isAdmin(){
        return $this->role === 1;
    }
}
