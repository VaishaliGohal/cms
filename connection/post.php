<?php

    class Post{

        public function fetch_all(){
            global $db;

            $query = $db->prepare("SELECT * FROM post ORDER BY post_id DESC");
            $query->execute();

            return $query->fetchAll();
        }

        public function fetch_data($id, $p){
            global $db;

            $query = $db->prepare("SELECT * FROM post WHERE post_id = ? AND slug = ?");
            $query->bindValue(1, $id);
            $query->bindValue(2, $p);
            $query->execute();

            return $query->fetch();

        }

        public function fetch_count($id, $p){
            global $db;

            $query = $db->prepare("SELECT * FROM post WHERE post_id = ? AND slug = ?");
            $query->bindValue(1, $id);
            $query->bindValue(2, $p);
            $query->execute();

            return $query->rowCount();

        }

        public function fetch_join_data($post_id){
            global $db;

            $query = $db->prepare("SELECT post.post_id, post.title, post.description, post.posted_on, post.image,
                                          cusine.cuisine_id, post.cuisine_id, cusine.cuisines
                                FROM post LEFT JOIN cusine ON post.cuisine_id = cusine.cuisine_id
                                WHERE post.post_id = ?;");
            
            $query->bindValue(1, $post_id);
            $query->execute();

            return $query->fetch();

        }

        public function fetch_search_term($search, $offset, $limit){
            global $db;

            $query = $db->prepare("SELECT * FROM post
                                WHERE title LIKE '%{$search}%'
                                ORDER BY post_id DESC
                                LIMIT {$offset}, {$limit}");
            
            $query->execute();
            return $query->fetchAll();

        }

    }
    
/*slug func*/
    function slug($text){
        $text = preg_replace('~[^\\pL\d]+~u','-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        if(empty($text)){
            return 'n-a';
        }

        return $text;
    }

?>
