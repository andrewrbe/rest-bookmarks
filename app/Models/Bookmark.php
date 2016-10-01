<?php

namespace Models {

    use Pimple\Container;
    use Pimple\ServiceProviderInterface;

    class Bookmark implements ServiceProviderInterface
    {


        /**
         * @var \Doctrine\DBAL\Connection
         */
        private $db;

        public function setDb($db)
        {
            $this->db = $db;
        }

        public function register(Container $app)
        {
            $app['model.bookmark'] = new self();
            $app['model.bookmark']->setDb($app['db']);
        }

        public function getLast()
        {
            return $this->db->fetchAll("SELECT uid, created_at, url FROM bookmarks ORDER by created_at DESC LIMIT 10");
        }

        public function getByUrl($url)
        {
            $bookmark = $this->db->fetchAssoc("SELECT id, uid, created_at, url FROM bookmarks WHERE url = ?", [$url]);
            if (!$bookmark) {
                return [];
            }

            $comments = $this->db->fetchAll("SELECT uid, created_at, ip, text FROM comments WHERE bookmark_id = ?", [$bookmark['id']]);
            $bookmark['comments'] = $comments ? $comments : [];
            unset($bookmark['id']);
            return $bookmark;
        }

        public function getOrCreateUid($url)
        {
            $bookmark = $this->db->fetchAssoc("SELECT * FROM bookmarks WHERE url = ?", [$url]);
            if ($bookmark) {
                $uid = $bookmark['uid'];
            } else {
                $uid = uniqid();
                $this->db->insert('bookmarks',
                    ['uid' => $uid, 'url' => $url, 'created_at' => date('c')]
                );
            }

            return ['uid' => $uid];

        }


    }
}

