<?php
    class Movie{
        public $id;
        public $name;
        public $img;
        public $times = array();

        public function __construct($id, $name, $img_path)
        {
            $this->id = $id;
            $this->name = $name;
            if(file_exists ("assets/".$img_path)){
                $this->img = "assets/".$img_path;
            }
            else{
                $this->img = "assets/default.jpg";
            }
        }
    }
    class mv_times{
        public $id;
        public $room;
        public $start;
        public $end;

        public function __construct($id, $room, $start, $end)
        {
            $this->id = $id;
            $this->room = $room;
            $this->start = $start;
            $this->end = $end;
            
        }
    }
?>