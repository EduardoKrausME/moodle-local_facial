<?php
/**
 * User: Eduardo Kraus
 * Date: 15/06/2018
 * Time: 13:47
 */

namespace local_facial\apis\aws;

class server_proccess {
    /**
     * @param $imageFile
     * @param $courseid
     * @param $userid
     *
     * @return array
     */
    public static function sendCapture($imageFile, $courseid, $userid) {

        $mimetype = "image/" . pathinfo($imageFile, PATHINFO_EXTENSION);
        $postname = pathinfo($imageFile, PATHINFO_BASENAME);

        $post = array(
            'file' => new \CURLFile($imageFile, $mimetype, $postname),
            'course' => $courseid,
            'user' => $userid
        );

        return self::send('Face/compare', $post);
    }

    public static function listCaptures($courseid, $userid) {
        $post = array(
            "course" => $courseid,
            "user" => $userid,
        );

        return self::send('Foto/list', $post);
    }

    /**
     * @param $keyFile
     */
    public static function deleteCapture($keyFile) {
        $post = array();

        self::send('deleteCapture', $post);
    }

    /**
     * @param $metodo
     * @param $post
     *
     * @return array
     */
    private static function send($metodo, $post) {
        try {
            $url = get_config('local_facial', 'url');
            $url = preg_replace('/(https:\/\/.*?\/).*/', '$1', $url);
            $token = get_config('local_facial', 'token');
        } catch (\dml_exception $e) {
            sendError($e->getMessage());
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$url}api/v1/{$metodo}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["authorization: {$token}"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}