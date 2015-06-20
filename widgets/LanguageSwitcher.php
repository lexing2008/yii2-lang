<?php
namespace xfg\lang\widgets;

use Yii;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * @author Timofey Suchkov <timofey.web@gmail.com>
 */
class LanguageSwitcher extends ButtonDropdown
{
  /**
   * Renders the language drop down if there are currently more than one
   * languages in the app. If you pass an associative array of language names
   * along with their code to the URL manager those language names will be
   * displayed in the drop down instead of their codes.
   */
  public function run()
  {
    $languages = isset(Yii::$app->urlManager->languages)
      ? Yii::$app->urlManager->languages
      : [];
    if (count($languages) > 1) {
      $items = [];
      $route = '/' . Yii::$app->controller->route;
      $params = Yii::$app->request->queryParams;
      $currentCode = array_search(Yii::$app->language, $languages);
      unset($params[Yii::$app->urlManager->routeParam]);
      unset($languages[$currentCode]);
      $this->encodeLabel = false;
      $this->label = '<span class="lang-sm lang-lbl" lang="'.$currentCode.'">' .
        '</span>';
      $this->options['class'] = 'btn btn-default';
      $this->dropdown['options']['class'] = 'dropdown-menu-right';
      $this->dropdown['encodeLabels'] = false;
      foreach ($languages as $code => $language) {
        $item = [
          'label' => '<span class="lang-sm lang-lbl" lang="'.$code.'"></span>',
          'url' => Url::to(
            ArrayHelper::merge([$route, 'lang' => $code], $params)
          )
        ];
        $items[] = $item;
      }
      $this->dropdown['items'] = $items;
      return parent::run();
    }
  }
}
