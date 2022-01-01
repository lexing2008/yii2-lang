<?php
namespace lexing2008\yii2-lang\components;

use Yii;

/**
 * @author Timofey Suchkov <timofey.web@gmail.com>
 */
class UrlManager extends \yii\web\UrlManager
{
  /**
   * @var array Supported languages
   */
  public $languages = [];
  /**
   * @var string Parameter used to set the language
   */
  public $languageParam = 'lang';

  /**
   * @inheritdoc
   */
  private $_ruleCache;


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
   * @inheritdoc
   */
  public function createUrl($params)
  {
    $params = (array) $params;
    $defaultLanguage = array_search(Yii::$app->language, $this->languages);
    $language = isset($params[$this->languageParam]) ? $params[$this->languageParam] : $defaultLanguage;
    $anchor = isset($params['#']) ? '#' . $params['#'] : '';
    unset($params[$this->languageParam], $params['#'], $params[$this->routeParam]);

    $route = trim($params[0], '/');
    unset($params[0]);

    $baseUrl = $this->showScriptName || !$this->enablePrettyUrl ? $this->getScriptUrl() : $this->getBaseUrl();

    if ($this->enablePrettyUrl) {
        $cacheKey = $route . '?' . implode('&', array_keys($params));

        /* @var $rule UrlRule */
        $url = false;
        if (isset($this->_ruleCache[$cacheKey])) {
            foreach ($this->_ruleCache[$cacheKey] as $rule) {
                if (($url = $rule->createUrl($this, $route, $params)) !== false) {
                    break;
                }
            }
        } else {
            $this->_ruleCache[$cacheKey] = [];
        }

        if ($url === false) {
            foreach ($this->rules as $rule) {
                if (($url = $rule->createUrl($this, $route, $params)) !== false) {
                    $this->_ruleCache[$cacheKey][] = $rule;
                    break;
                }
            }
        }

        if ($url !== false) {
            if (strpos($url, '://') !== false) {
                if ($baseUrl !== '' && ($pos = strpos($url, '/', 8)) !== false) {
                    $path = rtrim('/' . $language . substr($url, $pos));
                    return substr($url, 0, $pos) . $baseUrl  . $path . $anchor;
                } else {
                    return $url . $baseUrl . rtrim('/' . $language) . $anchor;
                }
            } else {
              $url = trim($language . '/' . $url, '/');
              return "$baseUrl/{$url}{$anchor}";
            }
        }

        if ($this->suffix !== null) {
            $route .= $this->suffix;
        }
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $route .= '?' . $query;
        }

        $route = trim($language . '/' . $route, '/');

        return "$baseUrl/{$route}{$anchor}";
    } else {
        $route = trim($language . '/' . $route, '/');
        $url = "$baseUrl?{$this->routeParam}=" . urlencode($route);
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '&' . $query;
        }

        return $url . $anchor;
    }
  }
}
