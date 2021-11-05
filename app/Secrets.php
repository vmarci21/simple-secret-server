<?php
namespace App;

class Secrets {
    private static ?Secrets $instance = null;

    private function __construct(){

    }
    
    public static function getInstance(): Secrets {
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addSecret($text, $expireAfter, $expireAfterViews){
        if(!is_numeric($expireAfterViews) or !is_numeric($expireAfter) or $expireAfterViews<1 or $expireAfter<0){
            return false;
        }
        $data = [
            'hash' => uniqid(),
            'secretText' => $text,
            'createdAt' => date('Y-m-d H:i:s'),
            'expiresAt' => (($expireAfter<1) ? null : date('Y-m-d H:i:s',strtotime('+'.$expireAfter.' minutes'))),
            'expireAfterViews' => $expireAfterViews
        ];
        $success = \DB::insert('secrets',$data);

        if($success) {
            $data['hash'] = \DB::insertId().'-'.$data['hash'];
            $data['remainingViews'] = $data['expireAfterViews'];
            unset($data['expireAfterViews']);
            return $data;
        }

        return false;
    }

    public function getSecretByHash(string $hash){
        $parts = explode('-',$hash);

        // invalid hash format
        if(count($parts) !== 2){
            return false;
        }

        \DB::query("UPDATE secrets SET views = views + 1 WHERE id=%i and hash=%s", $parts[0],$parts[1]);
        $row = \DB::queryFirstRow("SELECT secretText, createdAt, expiresAt, expireAfterViews, views FROM secrets WHERE id=%i and hash=%s LIMIT 1", $parts[0],$parts[1]);

        if(empty($row)){
            return false;
        }
        if($row['expireAfterViews'] - $row['views'] < 0){
            return false;
        }
        if(strtotime($row['expiresAt']) < time()){
            return false;
        }

        $data = [
            'hash' => $hash,
            'secretText'=>$row['secretText'],
            'createdAt'=>$row['createdAt'],
            'expiresAt'=>$row['expiresAt'],
            'remainingViews'=>$row['expireAfterViews'] - $row['views'],
        ];

        return $data;
    }
}