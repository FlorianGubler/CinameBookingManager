<?php
    class Movie{
        public $id;
        public $name;
        public $img;
        public $fsk;
        public $description;
        public $times = array();

        public function __construct($id, $name, $img_path, $fsk, $description, $location)
        {
            $this->id = $id;
            $this->name = $name;
            $this->fsk = $fsk;
            $this->description = $description;
            
            if(file_exists ($location."/image/".$img_path)){ //From Config.php File
                $this->img = $img_path;
            }
            else{
                $this->img = "default.jpg";
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