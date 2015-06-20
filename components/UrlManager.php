<?php
namespace xfg\lang\components;

use Yii;

/**
 * @author Timofey Suchkov <timofey.web@gmail.com>
 */
class UrlManager extends \yii\web\UrlManager
{
  /**
   * @var array Supported languages
   */
  public $languages;
  /**
   * @var bool Whether to display the source app language in the URL
   */
  public $displaySourceLanguage = false;
  /**
   * @var string Parameter used to set the language
   */
  public $languageParam = 'lang';


  /**
   * @inheritdoc
   */
  public function init()
  {
    if (
      empty($this->languages)
      && preg_match('/([a-z]{2,3})-[A-Z]{2}/', Yii::$app->language, $matches)
    ) {
      $this->languages = [$matches[1] => Yii::$app->language];
    }
    parent::init();
  }

  /**
   * Parses the URL and sets the language accordingly
   * @param \yii\web\Request $request
   * @return array|bool
   */
  public function parseRequest($request)
  {
    if ($this->enablePrettyUrl) {
      $pathInfo = $request->getPathInfo();
      $language = explode('/', $pathInfo)[0];
      if (array_key_exists($language, $this->languages)) {
        $request->setPathInfo(
          substr_replace($pathInfo, '', 0, (strlen($language) + 1))
        );
        Yii::$app->language = $this->languages[$language];
      }
    } else {
      $route = $request->getQueryParam($this->routeParam, '');
      if (is_array($route)) {
        $route = '';
      }
      $language = explode('/', $route)[0];
      if (array_key_exists($language, $this->languages)) {
        $route = substr_replace($route, '', 0, (strlen($language) + 1));
        $params = Yii::$app->request->queryParams;
        $params[$this->routeParam] = $route;
        $request->setQueryParams($params);
        Yii::$app->language = $this->languages[$language];
      }
    }
    return parent::parseRequest($request);
  }

  /**
   * Adds language functionality to URL creation
   * @param array|string $params
   * @return string
   */
  public function createUrl($params)
  {
    $url = parent::createUrl($params);
    if (array_key_exists($this->languageParam, $params)) {
      $language = $params[$this->languageParam];
      if (
        array_key_exists($language, $this->languages)
        && (
          $language !== Yii::$app->sourceLanguage
          || $this->displaySourceLanguage
        )
      ) {
        if ($this->enablePrettyUrl) {
          $str = '/' . $language . $url;
        } else {
          $r = explode($this->routeParam, $url, 2);
          $route = urlencode($language . '/') . substr($r[1], 1);
          $pos = strpos($url, $this->routeParam) + strlen($this->routeParam) + 1;
          $str = substr_replace($url, $route, $pos, strlen($route));
        }
        $pattern = '/(\?|&)' . $this->languageParam . '=' . $language . '/';
        $url = preg_replace($pattern, '', $str);
      }
    } else {
      if (
        (
          Yii::$app->language !== Yii::$app->sourceLanguage
          || $this->displaySourceLanguage
        )
      ) {
        $language = array_search(Yii::$app->language, $this->languages);
        if ($this->enablePrettyUrl) {
          $url = '/' . $language . $url;
        } else {
          $r = explode($this->routeParam, $url, 2);
          $route = urlencode($language . '/') . substr($r[1], 1);
          $pos = strpos($url, $this->routeParam) + strlen($this->routeParam) + 1;
          $url = substr_replace($url, $route, $pos, strlen($route));
        }
      }
    }
    return $url;
  }
}
