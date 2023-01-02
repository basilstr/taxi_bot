<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AccountForm extends Model
{
    public $username;
    public $name;
    public $email;
    public $password;
    public $password_repeat;
    public $phone;
    public $avatar;
    public $filename;

    public function init()
    {
        parent::init();

        $user = User::findIdentity(\Yii::$app->user->identity->getId());
        if(!empty($user)) {
            $this->username = $user->username;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->avatar = $user->avatar;
        }
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required', 'message'=>"Поле обо'язкове до заповнення"],
            ['name', 'string', 'min' => 3, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required', 'message'=>"Поле обо'язкове до заповнення"],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email',
                'unique',
                'targetClass' => '\app\models\User',
                'message' => 'така поштова скринька вже існує.',
                'when' => function ($model){
                    $email = User::find()->where(['email'=>$model->email])->andWhere(['<>', 'id', \Yii::$app->user->identity->getId()])->count();
                    return $email > 0;
                }
            ],
            [['phone'], 'string'],
            ['phone', 'match', 'pattern' => '/^(38)(\d{10})/', 'message' => 'Телефон повинен бути в форматі 38XXXXXXXXXX'],
            [['password','password_repeat'], 'string'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat', 'message'=>"Паролі не співпадають"],
            [['avatar'], 'safe'],
            [['avatar'], 'file', 'extensions'=>'jpg, gif, png'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'ім\'я',
            'email' => 'поштова адреса',
            'password' => 'пароль',
            'password_repeat' => 'пароль',
            'phone' => 'контактний телефон',
            'avatar' => '',
        ];
    }

    public function updateData()
    {

        if (!$this->validate()) {
            return null;
        }

        $image = UploadedFile::getInstance($this, 'avatar');
        if(!empty($image)) {
            // store the source file name
            $this->filename = $image->name;
            $ext = explode(".", $image->name);
            $ext = end($ext);

            // generate a unique file name
            //$this->avatar = Yii::$app->security->generateRandomString() . ".{$ext}";
            $this->avatar ='avatar_' .\Yii::$app->user->identity->getId(). ".{$ext}";

            $path = Yii::$app->basePath . '/web/uploads/avatars/' . $this->avatar;
            $image->saveAs($path);
        }
        $user = User::findIdentity(\Yii::$app->user->identity->getId());
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        // якщо аватар не завантажений, то він при зхавантаженні форми відсутній. Повертаємо йому значення з бази
        if(!empty($image)){
            $user->avatar = $this->avatar;
        }else{
            $this->avatar = $user->avatar;
        }

        if(!empty($this->password)) {
            $user->setPassword($this->password);
            $user->generateAuthKey();
        }
        return $user->save(false) ? $user : null;
    }
}