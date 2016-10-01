<?php

namespace Models {

    use DateTime;
    use Pimple\Container;
    use Pimple\ServiceProviderInterface;

    class Comment implements ServiceProviderInterface
    {
        /**
         * @var \Doctrine\DBAL\Connection
         */
        private $db;

        /**
         * @param mixed $db
         */
        public function setDb($db)
        {
            $this->db = $db;
        }

        public function register(Container $app)
        {
            $app['model.comment'] = new self();
            $app['model.comment']->setDb($app['db']);
        }

        public function add($uid, $text, $ip)
        {
            $bookmark = $this->db->fetchAssoc("SELECT id FROM bookmarks WHERE uid = ?", [$uid]);
            if (!$bookmark) {
                return ['error' => 'Bookmark was not found.'];
            }

            $uid = uniqid();
            $this->db->insert('comments',
                ['bookmark_id' => $bookmark['id'], 'uid' => $uid, 'text' => $text, 'ip' => $ip, 'created_at' => date('c')]
            );
            return ['uid' => $uid];
        }

        public function edit($uid, $text, $ip)
        {

            $comment = $this->db->fetchAssoc("SELECT ip, created_at FROM comments WHERE uid = ?", [$uid]);
            if (!$comment) {
                return ['error' => 'Comment was not found'];
            }

            if ($comment['ip'] != $ip) {
                return ['error' => 'Forbidden'];
            }

            $commentDate = new DateTime($comment['created_at']);
            $nowDate = new DateTime();
            $hoursDiff = $commentDate->diff($nowDate)->h;

            if ($hoursDiff >= 1) {
                return ['error' => "You can't edit comment after an hour."];
            }

            $this->db->update('comments', ['text' => $text], ['uid' => $uid]);
            return ['success' => true];
        }

        public function delete($uid, $ip)
        {
            $comment = $this->db->fetchAssoc("SELECT ip, created_at FROM comments WHERE uid = ?", [$uid]);
            if (!$comment) {
                return ['error' => 'Comment was not found'];
            }

            if ($comment['ip'] != $ip) {
                return ['error' => 'Forbidden'];
            }

            $commentDate = new DateTime($comment['created_at']);
            $nowDate = new DateTime();
            $hoursDiff = $commentDate->diff($nowDate)->h;

            if ($hoursDiff >= 1) {
                return ['error' => "You can't delete comment after an hour."];
            }
            $this->db->delete('comments', ['uid' => $uid]);
            return ['success' => true];
        }

    }
}

