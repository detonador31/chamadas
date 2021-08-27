<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'order',
    ];

    protected $dates = [
        'deleted_at'
    ];  
    
    public function menus()
    {
        return $this->hasMany(related: Menu::class);
    }        
}