<?php

namespace App\Helpers\Auth;

use Spatie\Permission\Exceptions\UnauthorizedException;
use App\Models\Auth\User;

/**
 * Class Socialite.
 */
class PermissionHelper
{
    /**
     * List of the accepted third party provider types to login with.
     *
     * @return array
     */
    public static function checkViewPermission($checkingObject)
    {
        if (!isset($checkingObject)) {
            throw new UnauthorizedException(403);
        }
        if (!auth()->user()->can('showAny', $checkingObject)
            && !auth()->user()->can('show', $checkingObject)) {
                throw new UnauthorizedException(403);
        }
    }

    public static function checkEditPermission($checkingObject)
    {
        if (!isset($checkingObject)) {
            throw new UnauthorizedException(403);
        }
        if (!auth()->user()->can('update', $checkingObject)) {
                throw new UnauthorizedException(403);
        }
    }

    public static function checkChangePasswordPermission($user)
    {
        if (!isset($user)) {
            throw new UnauthorizedException(403);
        }
        if (!auth()->user()->can('editPass', $user)) {
                throw new UnauthorizedException(403);
        }
    }

    public static function validRole($editUser)
    {
        if (!isset($editUser)) {
            throw new UnauthorizedException(403);
        }
        if (!auth()->user()->can('validRole', $editUser)) {
                throw new UnauthorizedException(403);
        }
    }

    public static function validRoleForList($associatedId, $editRole)
    {
        if (!isset($editRole) || !isset($associatedId)) {
            throw new UnauthorizedException(403);
        }
        
        if (!auth()->user()->can('validRoleForList', [auth()->user(), $associatedId, $editRole])) {
                throw new UnauthorizedException(403);
        }
    }
}
