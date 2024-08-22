<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

use App\Core\Parents\Enums\InstantiableEnum;

/**
 * @method static \App\Core\Values\Enums\ResponseTypeEnum UNKNOWN()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum GENERAL()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum UNAUTHENTICATED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum TWO_FACTOR_UNAUTHENTICATED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum UNAUTHORIZED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum NOT_FOUND()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum METHOD_NOT_ALLOWED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum DUPLICITY()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum CONFLICT()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum LOCKED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum LENGTH_REQUIRED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum FILE_UPLOAD_ERROR()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum INVALID_RESOURCE_PARAMETERS()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum INVALID_COMBINATION()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum VALIDATION_ERROR()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum SHOP_NOT_RECOGNIZED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum FORBIDDEN()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum BAD_USAGE()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum GONE()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum EXPIRED_TOKEN()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum CONDITIONS_NOT_MET()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum NOT_IMPLEMENTED()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum SERVICE_ERROR()
 * @method static \App\Core\Values\Enums\ResponseTypeEnum TOO_MANY_REQUESTS()
 */
final class ResponseTypeEnum extends InstantiableEnum
{
    public const string UNKNOWN = 'Unknown';

    public const string GENERAL = 'GeneralError';

    public const string UNAUTHENTICATED = 'Unauthenticated';

    public const string TWO_FACTOR_UNAUTHENTICATED = 'TwoFactorUnauthenticated';

    public const string UNAUTHORIZED = 'Unauthorized';

    public const string NOT_FOUND = 'NotFound';

    public const string METHOD_NOT_ALLOWED = 'MethodNotAllowed';

    public const string DUPLICITY = 'Duplicity';

    public const string CONFLICT = 'Conflict';

    public const string LOCKED = 'Locked';

    public const string LENGTH_REQUIRED = 'LengthRequired';

    public const string FILE_UPLOAD_ERROR = 'FileUploadError';

    public const string INVALID_RESOURCE_PARAMETERS = 'InvalidResourceParameters';

    public const string INVALID_COMBINATION = 'InvalidCombination';

    public const string VALIDATION_ERROR = 'ValidationError';

    public const string SHOP_NOT_RECOGNIZED = 'ShopNotRecognized';

    public const string FORBIDDEN = 'Forbidden';

    public const string BAD_USAGE = 'BadUsage';

    public const string GONE = 'Gone';

    public const string EXPIRED_TOKEN = 'ExpiredToken';

    public const string CONDITIONS_NOT_MET = 'ConditionsNotMet';

    public const string NOT_IMPLEMENTED = 'NotImplemented';

    public const string SERVICE_ERROR = 'ServiceError';

    public const string TOO_MANY_REQUESTS = 'TooManyRequests';
}
