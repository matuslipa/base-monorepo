<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

final class ModelEventEnum
{
    public const string CREATING = 'creating';

    public const string CREATED = 'created';

    public const string SAVING = 'saving';

    public const string SAVED = 'saved';

    public const string SAVE_FINISHED = 'save_finished';

    public const string UPDATED = 'updated';

    public const string UPDATING = 'updating';

    public const string DELETING = 'deleting';

    public const string DELETED = 'deleted';

    public const string BEFORE_DELETED = 'deleted';
}
