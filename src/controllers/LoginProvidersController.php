<?php
/**
 * @link      https://dukt.net/craft/social/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/social/docs/license
 */

namespace dukt\social\controllers;

use Craft;
use craft\web\Controller;
use dukt\social\web\assets\social\SocialAsset;
use dukt\social\Plugin as Social;
use yii\web\HttpException;

class LoginProvidersController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * Login Providers Index
     *
     * @return null
     */
    public function actionIndex()
    {
        Craft::$app->getView()->registerAssetBundle(SocialAsset::class);

        $variables['loginProviders'] = Social::$plugin->getLoginProviders()->getLoginProviders(false);

        return $this->renderTemplate('social/loginproviders/_index', $variables);
    }

    /**
     * Edit Login Provider
     *
     * @param array $variable Route variables
     *
     * @throws HttpException
     * @return null
     */
    public function actionEdit($handle)
    {
        $loginProvider = Social::$plugin->getLoginProviders()->getLoginProvider($handle, false, true);

        if ($loginProvider)
        {
            $infos = Social::$plugin->getOauth()->getProviderInfos($handle);

            $configInfos = Craft::$app->getConfig()->get('providerInfos', 'social');

            return $this->renderTemplate('social/loginproviders/_edit', [
                'handle' => $handle,
                'infos' => $infos,
                'configInfos' => $configInfos,
                'loginProvider' => $loginProvider

            ]);
        }
        else
        {
            throw new HttpException(404);
        }
    }

    /**
     * Enable Login Provider
     *
     * @return null
     */
    public function actionEnableLoginProvider()
    {
        $this->requirePostRequest();
        $loginProvider = Craft::$app->getRequest()->getRequiredBodyParam('loginProvider');

        if (Social::$plugin->getLoginProviders()->enableLoginProvider($loginProvider))
        {
            Craft::$app->getSession()->setNotice(Craft::t('app', 'Login provider enabled.'));
        }
        else
        {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t enable login provider.'));
        }

        return $this->redirectToPostedUrl();
    }

    /**
     * Disable Login Provider
     *
     * @return null
     */
    public function actionDisableLoginProvider()
    {
        $this->requirePostRequest();
        $loginProvider = Craft::$app->getRequest()->getRequiredBodyParam('loginProvider');

        if (Social::$plugin->getLoginProviders()->disableLoginProvider($loginProvider))
        {
            Craft::$app->getSession()->setNotice(Craft::t('app', 'Login provider disabled.'));
        }
        else
        {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t disable login provider.'));
        }

        return $this->redirectToPostedUrl();
    }
}