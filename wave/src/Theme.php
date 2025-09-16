<?php

declare(strict_types=1);

namespace Wave;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'themes';

    protected $fillable = ['name', 'folder', 'version'];

    public function options()
    {
        return $this->hasMany(\Wave\ThemeOptions::class, 'theme_id');
    }
}
