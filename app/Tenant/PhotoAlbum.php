<?php

namespace App\Tenant;

use App\Tenant\Model;

class PhotoAlbum extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'photo_album as t1';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public static function getPhotoAlbum($userId)
    {
        $result = self::select('t1.id as album_id', 't3.id as photo_id', 't3.hash')
                  ->join('protectedphotos_passwords as t2' , 't2.albumId', '=', 't1.id')
                  ->join('photo as t3' , 't3.albumId', '=', 't1.id')
                  ->where('t1.userId', $userId)
                  ->where('t2.privacy', 'password')->get();

        return $result;
    }

}