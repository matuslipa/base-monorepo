<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

use App\Core\Parents\Enums\InstantiableEnum;

/**
 * Class FilesystemDiskEnum
 *
 * @method static FilesystemDiskEnum LOCAL()
 * @method static FilesystemDiskEnum LOCAL_IMAGES()
 * @method static FilesystemDiskEnum MEDIA()
 * @method static FilesystemDiskEnum UPLOADS()
 * @method static FilesystemDiskEnum PUBLIC()
 *
 * @package App\Core\Values\Enums
 */
final class FilesystemDiskEnum extends InstantiableEnum
{
    public const string LOCAL_IMAGES = 'local_images';

    public const string UPLOADS = 'uploads';

    public const string LOCAL = 'local';

    public const string MEDIA = 'media';

    public const string PUBLIC = 'public';
}
