<?php

class File extends ModelBase
{

    public $id;
    
    public $slug;
    
    public $board;
    
    public $type;
    
    public $owner;
    
    public function getLink( $type = 'origin')
    {   
        if ( $type == 'origin' )
            return '/img/' . $this->slug . '.' . $this->type;
        else
        if ( $type == 'thumb' )
            return '/img/' . $this->slug . '_t.' . $this->type;
    }
    
    public function initialize()
    {
        $this->belongsTo("owner", "Post", "id");
    }
    
    // Удаляя модель удалим и файлы
    public function beforeDelete()
    {
        unlink( getLink('origin') );
        unlink( getLink('thumb')  );
    }
    
}
