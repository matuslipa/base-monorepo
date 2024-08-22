<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Parents\Models\Model;

final class FrontException extends \RuntimeException
{
    public const int GENERAL_ERROR_CODE = 0;

    public const int SETTINGS_FORM_FACTORY_BINDING_FAILED = 2;

    public const int SYSTEM_CONFIG_NOT_ARRAY = 3;

    public const int CONTROLLER_NOT_FOUND_FOR_MODEL = 4;

    public const int CONTROLLER_FOR_MODEL_BINDING_FAILED = 5;

    public const int INVALID_CONTROLLER_INSTANCE = 6;

    public const int MISSING_MIX_MANIFEST = 7;

    public const int MISSING_CONTROLLER_ACTION = 8;

    public const int RENDER_RESOLVER_BINDING_FAILED = 9;

    public const int INVALID_MODEL_RENDER_RESOLVER_INSTANCE = 10;

    public const int CONTROLLER_FOR_HOMEPAGE_BINDING_FAILED = 11;

    public const int RENDER_RESOLVER_NOT_FOUND = 12;

    /**
     * Additional parameters.
     *
     * @var mixed[]
     */
    private array $parameters = [];

    /**
     * Thrown when binding of settings form factory fails.
     *
     * @param \Throwable $previous
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function settingsFormFactoryBindingFailed(\Throwable $previous): self
    {
        return new self(
            'Binding settings form factory failed!',
            self::SETTINGS_FORM_FACTORY_BINDING_FAILED,
            $previous
        );
    }

    /**
     * Thrown when registering system config is not possible.
     *
     * @param string $key
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function systemConfigNotArray(string $key): self
    {
        return new self(
            'Registering system config failed, because value under key "' . $key . '" is not array!',
            self::SYSTEM_CONFIG_NOT_ARRAY
        );
    }

    /**
     * Thrown when unexpected exception occurs.
     *
     * @param \Throwable $previous
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function unexpectedError(\Throwable $previous): self
    {
        return new self(
            'Failed to import data.',
            self::GENERAL_ERROR_CODE,
            $previous
        );
    }

    /**
     * Thrown when controller cannot be found for viewed model.
     *
     * @param \App\Core\Parents\Models\Model $model
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function controllerNotFoundForModel(Model $model): self
    {
        $exception = new self(
            'Controller for model "' . $model::class . '" not found!',
            self::CONTROLLER_NOT_FOUND_FOR_MODEL
        );

        $exception->parameters = \compact('model');

        return $exception;
    }

    /**
     * Thrown when controller cannot be found for viewed model.
     *
     * @param string $controller
     * @param \App\Core\Parents\Models\Model $model
     * @param \Throwable $previous
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function controllerForModelBindingFailed(
        string $controller,
        Model $model,
        \Throwable $previous
    ): self {
        $exception = new self(
            "Binding of controller \"${controller}\" for model \"" . $model::class . '" failed!',
            self::CONTROLLER_FOR_MODEL_BINDING_FAILED,
            $previous
        );

        $exception->parameters = \compact('controller', 'model');

        return $exception;
    }

    /**
     * Thrown when controller cannot be found for viewed model.
     *
     * @param string $controller
     * @param \Throwable $previous
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function controllerForHomepageBindingFailed(
        string $controller,
        \Throwable $previous
    ): self {
        $exception = new self(
            "Binding of controller \"${controller}\" for homepage failed!",
            self::CONTROLLER_FOR_HOMEPAGE_BINDING_FAILED,
            $previous
        );

        $exception->parameters = \compact('controller');

        return $exception;
    }

    /**
     * Thrown when controller cannot be found for viewed model.
     *
     * @param string $controller
     * @param string $expected
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function invalidControllerInstance(string $controller, string $expected): self
    {
        return new self(
            'Controller \"' . $controller . '" must be descendant of class "' . $expected . '"!',
            self::INVALID_CONTROLLER_INSTANCE
        );
    }

    /**
     * Thrown when mix manifest cannot be found.
     *
     * @param \Throwable $previous
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function missingMixManifest(\Throwable $previous): self
    {
        return new self(
            'Mix manifest cannot be found for your Front!',
            self::MISSING_MIX_MANIFEST,
            $previous
        );
    }

    /**
     * Thrown when controller action is missing.
     *
     * @param string $action
     * @param string $controllerClass
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function controllerActionMissing(string $action, string $controllerClass): self
    {
        return new self(
            'Missing method "' . $action . '" in ' . $controllerClass . '!',
            self::MISSING_CONTROLLER_ACTION
        );
    }

    /**
     * Thrown when model render resolver has invalid instance.
     *
     * @param string $renderResolver
     * @param string $expected
     *
     * @return \App\Core\Exceptions\FrontException
     */
    public static function invalidModelRenderResolverInstance(string $renderResolver, string $expected): self
    {
        return new self(
            'Render resolver \"' . $renderResolver . '" must be descendant of class "' . $expected . '"!',
            self::INVALID_MODEL_RENDER_RESOLVER_INSTANCE
        );
    }

    /**
     * Get parameter.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
    }
}
