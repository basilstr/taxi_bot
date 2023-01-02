<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username ЛОГІН
 * @property string $role
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int $id_city Посилання на місто
 * @property int $status
 * @property string $avatar
 * @property string $created_at
 * @property string $updated_at
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE  = 1;
    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'role', 'name', 'email', 'phone'], 'required'],
            [['id_city', 'status'], 'integer'],
            [['username', 'name', 'email'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 16],
            [['phone'], 'string', 'max' => 12],
            [['username'], 'unique'],
            [['email'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логін',
            'role' => 'Роль',
            'name' => 'Ім\'я',
            'email' => 'Пошта',
            'phone' => 'Телефон',
            'id_city' => 'Місто',
            'status' => 'Статус',
            'created_at' => 'Створено',
            'updated_at' => 'Оновлено',
        ];
    }

    public static function listRole()
    {
        return ['admin'=>'адмін', 'user'=>'користувач'];
    }
    public function afterFind()
    {
        $this->avatar = empty($this->avatar) ? 'clientAvatar.jpg' : $this->avatar;
        return parent::afterFind();
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function isAdmin()
    {
        $user = self::findIdentity(\Yii::$app->user->identity->getId());
        return $user->role == 'admin';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::class, ['id' => 'id_city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasMany(Car::class, ['id_user' => 'id']);
    }

    /**
     * @param $message
     */
    public function sendMessage($message)
    {
        $informUser = InformUser::findAll(['id_user'=>$this->id]);
        foreach($informUser as $inform) {
            Telegram::sendMessage($inform->chat_id, $message);
        }
    }

    public function sendGeo($lat, $lon)
    {
        $informUser = InformUser::findAll(['id_user'=>$this->id]);
        foreach($informUser as $inform) {
            Telegram::sendGeo($inform->chat_id, $lat, $lon);
        }
    }
}
