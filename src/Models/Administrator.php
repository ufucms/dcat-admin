<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements AuthenticatableContract
{
    use Authenticatable,
        HasPermissions,
        HasDateTimeFormatter;

    public static $default_id = 1;

    protected $fillable = ['username', 'password', 'name', 'avatar'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        $this->setDefaultId();

        parent::__construct($attributes);
    }

    protected function init()
    {

        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));
    }

    protected function setDefaultId()
    {
        self::$default_id = config('admin.default_id') ?: 1;

        return $this;
    }

    /**
     * Get avatar attribute.
     *
     * @return mixed|string
     */
    public function getAvatar()
    {
        $avatar = $this->avatar;

        if ($avatar) {
            if (! URL::isValidUrl($avatar)) {
                $avatar = Storage::disk(config('admin.upload.disk'))->url($avatar);
            }

            return $avatar;
        }

        return admin_asset(config('admin.default_avatar') ?: '@admin/images/default-avatar.jpg');
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * 判断是否允许查看菜单.
     *
     * @param  array|Menu  $menu
     * @return bool
     */
    public function canSeeMenu($menu)
    {
        return true;
    }

    /**
     * Get default_id.
     *
     * @return mixed|string
     */
    public static function getDefaultId()
    {
        return (new static)->setDefaultId()::$default_id;
    }
}
