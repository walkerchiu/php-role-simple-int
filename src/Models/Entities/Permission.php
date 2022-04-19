<?php

namespace WalkerChiu\RoleSimple\Models\Entities;

use WalkerChiu\Core\Models\Entities\Entity;

class Permission extends Entity
{
    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.role-simple.permissions');

        $this->fillable = array_merge($this->fillable, [
            'serial', 'identifier',
            'name', 'description'
        ]);

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('wk-core.class.role-simple.role'),
                                    config('wk-core.table.role-simple.roles_permissions'),
                                    'permission_id',
                                    'role_id');
    }
}
