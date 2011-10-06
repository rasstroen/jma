<?php

class ContentWriteModule extends BaseWriteModule {

    function write() {
        if ($id = Request::post('id')) {
            $this->edit();
        } else {
            $this->enew();
        }
    }

    function getContentFilePath($id) {
        $filename = Config::need('static_path') . DIRECTORY_SEPARATOR . 'upload/pictures/' . $id . '.jpg';
        return $filename;
    }

    function enew() {
        $title = Request::post('title', '');
        $url = Request::post('url', '');
        $tags = Request::post('tags', '');
        Database::query('START TRANSACTION');
        $query = 'INSERT INTO `content_pictures` SET 
            `title`=' . Database::escape($title) . ',
            `time`=' . time();
        Database::query($query);
        $content_id = Database::lastInsertId();

        if ($url) {
            $data = file_get_contents($url);
            $filename = $this->getContentFilePath($content_id);
            file_put_contents($filename, $data);
            $upload = new UploadAvatar($filename, 600, 800, "simple", $filename);
            if (!$upload->out) {
                throw new Exception('cant copy file to ' . $filename);
            }
        } else
        if (isset($_FILES['file']) && $_FILES['file']['tmp_name']) {
            $filename = $this->getContentFilePath($content_id);
            $upload = new UploadAvatar($_FILES['file']['tmp_name'], 600, 800, "simple", $filename);
            if (!$upload->out) {
                throw new Exception('cant copy file to ' . $filename);
            }
        } else
            throw new Exception('No any file');


        $tags_prepared = array();
        if ($tags) {
            $tags = explode(',', $tags);
            foreach ($tags as $tag) {
                if (trim($tag))
                    $tags_prepared[trim($tag)] = Database::escape(trim($tag));
            }
        }
        $to_insert_tag = array();
        $tags_existing = array();
        if (count($tags_prepared)) {
            $query = 'SELECT * FROM `tags` WHERE `title` IN (' . implode(',', $tags_prepared) . ')';
            $tags_existing_db = Database::sql2array($query, 'title');
            foreach ($tags_prepared as $tag => $prepared) {
                if (!isset($tags_existing_db[$tag])) {
                    $to_insert_tag[$tag] = $prepared;
                } else {
                    $tags_existing[$tag] = $tags_existing_db[$tag]['id'];
                }
            }
        }

        if (count($to_insert_tag)) {
            foreach ($to_insert_tag as $tag) {
                Database::query('INSERT INTO `tags` SET `title`=' . $tag);
                $tags_existing[$tag] = Database::lastInsertId();
            }
        }

        if (count($tags_existing)) {
            $q = array();
            foreach ($tags_existing as $tag => $id) {
                $q[] = '(' . $content_id . ',' . $id . ')';
            }
            $query = 'INSERT INTO `content_pictures_tags`(`id_content_picture`,`id_tag`) VALUES ' . implode(',', $q);
            Database::query($query);
        }
        Database::query('COMMIT');
        if ($content_id) {
            header('Location: /pictures/' . $content_id);
            exit;
        }
    }

}