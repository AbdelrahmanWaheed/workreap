<?php
if (!class_exists('NotificationSystem')) {
    /**
     * One to One Chat System
     * 
     * @package Workreap
     */
    class NotificationSystem
    {
        
        /**
         * DB Variable
         * 
         * @var [string]
         */
        protected static $wpdb;
        /**
         * Initialize Singleton
         *
         * @var [void]
         */
        private static $_instance = null;

        /**
         * Call this method to get singleton
         *
         * @return ChatSystem Instance
         */
        public static function instance()
        {
            if (self::$_instance === null) {
                self::$_instance = new NotificationSystem();
            }
            return self::$_instance;
        }

        /**
         * PRIVATE CONSTRUCTOR
         */
        private function __construct()
        {
            global $wpdb;
            self::$wpdb = $wpdb;
            add_action('after_setup_theme', array(__CLASS__, 'createNotificationTable'));
        }

        /**
         * Create Notification Table
         *
         * @return void
         */
        public static function createNotificationTable()
        {
            $notifications = self::$wpdb->prefix . 'notifications';

            if (self::$wpdb->get_var("SHOW TABLES LIKE '$notifications'") != $notifications) {
                $charsetCollate = self::$wpdb->get_charset_collate();            
                $notificationsTable = "CREATE TABLE $notifications (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    user_id int(11) UNSIGNED NOT NULL,
                    message text NOT NULL,
                    url text NULL,
                    status tinyint(1) NOT NULL,
                    timestamp varchar(20) NOT NULL,
                    time_gmt datetime NOT NULL,
                    PRIMARY KEY (id)                           
                    ) $charsetCollate;";   
                                        
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($notificationsTable);
            }
        }

        /**
         * Send notification to user
         *
         * @return
         */
        public static function sendNotification($user_id, $message, $url = null) {
            $notifications = self::$wpdb->prefix . 'notifications';
            $data = array(
                'user_id'       => $user_id,
                'message'       => $message,
                'url'           => $url,
                'status'        => 1,
                'timestamp'     => current_time('timestamp'),
                'time_gmt'      => get_gmt_from_date(current_time('mysql')),
            );
            self::$wpdb->insert($notifications, $data);
            return self::$wpdb->insert_id;
        }

        public static function sendNotificationWithEmail($user_id, $message, $url, $subject) {
            self::sendNotification($user_id, $message, $url);

            if (class_exists('Workreap_Email_helper')) {
                if (class_exists('WorkreapSendNotification')) {
                    $email_helper = new WorkreapSendNotification();
                    $emailData    = array();

                    $emailData['user_name']     = workreap_get_username( $user_id );
                    $emailData['message']       = $message;
                    $emailData['email_to']      = get_userdata( $user_id )->user_email;

                    $email_helper->send($emailData);
                }
            }
        }

        /**
         * Mark notifications as seen
         *
         * @return
         */
        public static function markNotifications($user_id) {
            $notifications = self::$wpdb->prefix . 'notifications';
            self::$wpdb->update(
                $notifications,
                array("status" => intval(0)),
                array(
                    "user_id" => intval($user_id),
                    "status" => intval(1),
                )
            );
        }

        /**
         * Get User Notifications
         *
         * @return array
         */
        public static function getUserNotifications($user_id, $offset = 0, $total = 10) {
            $notifications = self::$wpdb->prefix . 'notifications';
            $limit = $offset * $total;
            $user_id = intval($user_id);

            $fetchResults = self::$wpdb->get_results("SELECT * FROM $notifications
                WHERE user_id = $user_id
                ORDER BY time_gmt DESC LIMIT $limit, $total", ARRAY_A
            );

            return $fetchResults;
        }

        /**
         * Get Total User Notifications Count
         *
         * @return array
         */
        public static function getTotalUserNotificationsCount($user_id) {
            $notifications = self::$wpdb->prefix . 'notifications';
            $user_id = intval($user_id);

            $count = self::$wpdb->get_var("SELECT count(*) FROM $notifications where user_id = $user_id");
            return $count;
        }

        /**
         * Get New User Notifications Count
         *
         * @return array
         */
        public static function getNewUserNotificationsCount($user_id) {
            $notifications = self::$wpdb->prefix . 'notifications';
            $user_id = intval($user_id);

            $count = self::$wpdb->get_var("SELECT count(*) FROM $notifications where user_id = $user_id and status = 1");
            return $count;
        }
    }

    NotificationSystem::instance();
}