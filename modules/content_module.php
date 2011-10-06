<?php

// модуль отвечает за отображение баннеров
class content_module extends BaseModule {

    function generateData() {
        global $current_user;
        $params = $this->params;

        switch ($this->action) {
            case 'list':
                switch ($this->mode) {
                    default:
                        $this->getAll();
                        break;
                }
                break;
            case 'show':
                switch ($this->mode) {
                    case 'random':
                       $this->getAll();
                        break;
                    default:
                        $this->getOne();
                        break;
                }
                break;
            case 'edit':
                $this->Edit();
                break;
            case 'new':
                $this->ENew();
                break;
            default:
                throw new Exception('no action #' . $this->action . ' for ' . $this->moduleName);
                break;
        }
    }

    function getAllPictures() {
        $query = 'SELECT * FROM `content_pictures` ORDER BY `time` DESC LIMIT 20';
        $data = Database::sql2array($query, 'id');
        foreach ($data as $pic) {
            $tmp = $pic;
            $tmp['time'] = date('Y/m/d H:i', $tmp['time']);
            $tmp['source'] = Config::need('www_path') . '/static/upload/pictures/' . $tmp['id'] . '.jpg';
            $this->data['pictures'][$pic['id']] = $tmp;
        }
        $tags = Database::sql2array('SELECT * FROM `content_pictures_tags` CPT
            LEFT JOIN `tags` T ON T.id = CPT.id_tag WHERE CPT.`id_content_picture` IN (' . implode(',', array_keys($data)) . ')');
        foreach ($tags as $tag) {
            $this->data['pictures'][$tag['id_content_picture']]['tags'][$tag['id_tag']] =
                    array(
                        'id_tag' => $tag['id_tag'],
                        `title` => $tag['title']
            );
        }
    }

    function getPicture() {
        $id = isset($this->params['id']) ? (int) $this->params['id'] : false;
        if (!$id) {
            throw new Exception('illegal picture id');
        }
        $query = 'SELECT * FROM `content_pictures` WHERE `id`=' . $id;
        $data = Database::sql2row($query);
        if (!$data)
            return;
        $data['time'] = date('Y/m/d H:i', $data['time']);
        $this->data['picture'] = $data;
        $this->data['picture']['source'] = Config::need('www_path') . '/static/upload/pictures/' . $data['id'] . '.jpg';

        $tags = Database::sql2array('SELECT `id_tag`,`title` FROM `content_pictures_tags` CPT
            LEFT JOIN `tags` T ON T.id = CPT.id_tag WHERE CPT.`id_content_picture`=' . $id);
        $this->data['picture']['tags'] = $tags;
    }

    function getOne() {
        $type = isset($this->params['type']) ? $this->params['type'] : false;
        switch ($type) {
            case 'picture':
                $this->getPicture();
                break;
            default:
                throw new Exception('illegal content type ' . $type);
                break;
        }
    }

    function getAll() {
        $type = isset($this->params['type']) ? $this->params['type'] : false;
        switch ($type) {
            case 'picture':
                $this->getAllPictures();
                break;
            default:
                $this->getAllPictures();
                break;
        }
    }

    function Edit() {
        
    }

    function ENew() {

    }

}