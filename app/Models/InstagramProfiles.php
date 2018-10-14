<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InstagramProfiles
 *
 * @property int $id
 * @property string $name
 * @property string $instagram_id
 * @property string $last_check
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereInstagramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereLastCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InstagramProfiles extends Model
{
    //
}
