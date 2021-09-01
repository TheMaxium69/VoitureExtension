<?php


class ExVoiture_Session
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function createMessage($type, $message)
    {
        $_SESSION['ex-voiture'] =
            [
                'type'    => $type,
                'message' => $message
            ];
    }

    public function getMessage()
    {
        return isset($_SESSION['ex-voiture']) && count($_SESSION['ex-voiture']) > 0 ? $_SESSION['ex-voiture'] : false;
    }

    public function destroy()
    {
        $_SESSION['ex-voiture'] = array();
    }
}
