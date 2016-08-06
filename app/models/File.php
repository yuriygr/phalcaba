<?php

class File extends ModelBase
{

    public $id;
    
    public $slug;
    
    public $board;
    
    public $type;
    
    public $owner;
    
    public $o_width;
    
    public $o_height;
    
    public function initialize()
    {
        $this->belongsTo("owner", "Post", "id");
    }
    // Удаляя модель удалим и файлы
    public function beforeDelete()
    {
        unlink( $this->getLink('origin') );
        unlink( $this->getLink('thumb')  );
    }
    // Получаем ссылку на файл
    public function getLink( $type = 'origin')
    {
        if ( $type == 'origin' )
            return '/file/' . $this->slug . '.' . $this->type;
            
        if ( $type == 'thumb' )
            return '/file/' . $this->slug . '_t.' . ($this->type != 'webm' ? $this->type : 'jpg') ;
    }
    // Получаем разрешение файла
    public function getResolution()
    {
        return $this->o_width . 'x' . $this->o_height;
    }
    
}
