Mailer
======
PHP and SMTP Mailer in Yii2 with dynamic configuration

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist samsher-bentray/yii2-mailer "*"
```

or add

```
"samsher-bentray/yii2-mailer": "*"
```

to the require section of your `composer.json` file.

Configuration
-------------

Once the extension is installed, add following code to your application configuration :

```php
return [
    //....
    'components' => [

            ...

            'email' => 'samsher\mailer\Mail',
            
            ...

    ],
];
```

Usage
-----

Once the extension is installed, simply use it in your code by using these codes as following way in the controller:

```php
public function actionCreate()

    {

        $model = new Email();



        if ($model->load(Yii::$app->request->post())) {
            
                /*Starting configuration for smtp or other type*/

                Yii::$app->email->setMailType('smtp');

                //Passing arguement for Host setting
                Yii::$app->email->setHost('smtp.gmail.com');

                //Passing arguement for Username setting
                Yii::$app->email->setUname('some email');

                //Passing arguement for Password setting
                Yii::$app->email->setPassd('some password');

                //Passing arguement for Encryption Type setting
                Yii::$app->email->setEncType('ssl');

                
                //Passing arguement for Port setting
                Yii::$app->email->setSMTPPort('465');

                /*Ending configuration for smtp or other type*/

                 
                Yii::$app->email->configSet();//note that email setting is completed only when execute this function


                $from = 'some email';

                $to = $model->to;

                $subject = $model->subject;

                $message_body = $model->text_body;

                $cc = $model->cc;

                $bcc = $model->bcc;

                /*Assigning the files for attachments*/

                $attachment = UploadedFile::getInstances($model,'attachment');
				
				Yii::$app->email->SaveAttach($attachment);

                // Syntax Yii::$app->email->SendMail($from,$to,$subject,$message_body,$cc,$bcc,$attachment);
                // It is important that default mail type is PHP mail
                // If we want to use PHP mail ,we can call only the function 
                // "Yii::$app->email->SendMail($from,$to,$subject,$message_body,$cc,$bcc,$attachment);"
                // If we want to use Smtp mail or other type, we can call the function
                // "Yii::$app->email->SendMail($from,$to,$subject,$message_body,$cc,$bcc,$attachment);"
                // only after the six setting for and running Yii::$app->email->configSet();

               if (Yii::$app->email->SendMail($from,$to,$subject,$message_body,$cc,$bcc,$attachment)){
			   
					//deleting the attachment
					Yii::$app->email->DeleteAttach($attachments);
                    Yii::$app->session->setFlash('success','Email sent.'); //for for wrong event.
                    return $this->redirect(['create']);
                }
                else {
                    Yii::$app->session->setFlash('danger','Email send not success.'); //for for wrong event.
                    return $this->redirect(['create']);
                }
        }

        else{

    

            return $this->render('create', [

                'model' => $model,

            ]);

        }

    }