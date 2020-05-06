<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model
{

    const SESSION = "Users";
    const SECRET = "HcodePhp7_Secret";

    public static function login($login, $password)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
        ));

        if(count($results) === 0)
        {
            throw  new \Exception("Usuário inexistente ou senha inválida.");
        }

        $data = $results[0];

        if(password_verify($password, $data["despassword"]))
        {
            // echo $data["despassword"];
            
            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user; 
            
        } else {
            //echo $data["despassword"];
            /*
            $user = new User();

            $user->setData($data);
            
            $_SESSION[User::SESSION] = $user->getValues();
        
            return $user; 
            */
            throw  new \Exception("Usuário inexistente ou senha inválida.");
        }   
        
    }


    public static function verifyLogin($inadmin = 1)
    {

        if (
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0
            ||
            (int)$_SESSION[User::SESSION]["inadmin"] !== $inadmin

        ) {
            header("Location: /ecommerce/admin/login");
            exit;
        }

    }

    public static function logout()
    {

        $_SESSION[User::SESSION] = NULL;
        // session_unset($_SESSION[User::SESSION]);

    }

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    public function save()
    {

        $sql = new Sql();
        /*
        pdesperson varchar(64),
        pdeslogin varchar(64),
        pdespassword varchar(256),
        pdesemail varchar(126),
        pnrphone bigint,
        pinadmin tinyint
        */
        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function get($iduser)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            ":iduser"=>$iduser
        ));

        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
            "iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));
        
    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }

    public static function getForgot($email)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT *
        FROM tb_persons a
        INNER JOIN tb_users b USING(idperson)
        WHERE a.desemail = :email;
        ", array(
            ":email"=>$email
        ));

        if (count($results) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha.");
        }
        else 
        {
            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));

            if (count($results2) === 0)
            {

                throw new \Exception("Não foi possível recuperar a senha.");

            }
            else 
            {

                $dataRecovery = $results2[0];
                
                // $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

                $iv = 'fuKJU6758gjrufdh';

                $code = base64_encode(openssl_encrypt($dataRecovery["idrecovery"], 'AES-128-CBC' , User::SECRET, OPENSSL_RAW_DATA, $iv ));

                $link = "localhost/ecommerce/admin/forgot/reset?code=$code";

                $mailer = new Mailer($data["desemail"],$data["desperson"],"Redefinir Senha da Hcode Store", "forgot", 
                array(
                    "name"=>$data["desperson"],
                    "link"=>$link
                ));

                $mailer->send();

                return $data;
            }
        }

    }

    public static function validForgotDecrypt($code)
    {
        $iv = 'fuKJU6758gjrufdh';

        base64_decode($code);

        $idrecovery = openssl_decrypt(base64_decode($code), 'AES-128-CBC' , User::SECRET, OPENSSL_RAW_DATA, $iv );

        $sql = new Sql();

        $results = $sql->select("
            SELECT * FROM tb_userspasswordsrecoveries a
            INNER JOIN tb_users b USING(iduser)
            INNER JOIN tb_persons c USING(idperson)
            where 
                a.idrecovery = :idrecorevy
                AND
                a.dtrecovery IS NULL
                AND
                date_add(a.dtregister, INTERVAL 2 hour) >= now();
        ", array(
            ":idrecorevy"=>$idrecovery
        ));
        

        if(count($results) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha.");
            //return $results[0];
        }
        else
        {
            return $results[0];
        }
        
    }

    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecorevy = :idr", array(
            ":idr"=>$idrecovery
        ));

    }

    public function setPassword($password)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
            ":password"=>$password,
            ":iduser"=>$this->getiduser()
        ));

    }



}

?>