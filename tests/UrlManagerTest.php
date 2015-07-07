<?php
namespace xfg\lang\tests;

use Yii;
use yii\codeception\TestCase;
use app\modules\core\web\UrlManager;

use Codeception\Specify;

/**
 * @author Timofey Suchkov <timofey.web@gmail.com>
 */
class UrlManagerTest extends TestCase
{
  use Specify;

  public function testParseRequest()
  {
    $model = new UrlManager();
    $model->languages = [
      'en' => 'en-US',
      'ru' => 'ru-RU',
      'fr' => 'fr-FR',
    ];

    Yii::$app->language = 'en-US';

    $this->specify('parse request sets up a default app language correctly', function () use ($model) {
      $model->enablePrettyUrl = true;
      Yii::$app->request->setPathInfo('page');
      $model->parseRequest(Yii::$app->request);
      expect('language is a default app language (pretty url)', Yii::$app->language)->equals('en-US');

      $model->enablePrettyUrl = false;
      Yii::$app->request->setQueryParams([
        'r' => 'page',
      ]);
      $model->parseRequest(Yii::$app->request);
      expect('language is a default app language', Yii::$app->language)->equals('en-US');
    });

    $this->specify('parse request toggles an app language correctly', function () use ($model) {
      $model->enablePrettyUrl = true;
      Yii::$app->request->setPathInfo('ru/page');
      $model->parseRequest(Yii::$app->request);
      expect('language is toggled (pretty url)', Yii::$app->language)->equals('ru-RU');

      $model->enablePrettyUrl = false;
      Yii::$app->request->setQueryParams([
        'r' => 'fr/page',
      ]);
      $model->parseRequest(Yii::$app->request);
      expect('language is toggled', Yii::$app->language)->equals('fr-FR');
    });
  }

  public function testCreateUrl()
  {
    $model = new UrlManager();

    $this->specify('create url should building correctly url addresses', function () use ($model) {
      $model->enablePrettyUrl = true;
      expect('url contains a language (pretty url)', $model->createUrl(['page', 'lang' => 'fr']))->equals('/index-test.php/fr/page');

      $model->enablePrettyUrl = false;
      expect('url contains a language', $model->createUrl(['page', 'lang' => 'fr']))->equals('/index-test.php?r=fr%2Fpage');
    });
  }
}
