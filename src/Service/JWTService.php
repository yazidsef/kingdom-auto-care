<?php
// ce fameux fichier et pour la confirmation d'email
namespace App\Service;

use DateTimeImmutable;

class JWTService 
{
    //on genere le token 
    public function generate(array $header , array $payload , string $secret , int $validity = 10800 ):string 
    {
        if($validity > 0){

        $now = new DateTimeImmutable();
        $exp = $now->getTimestamp() + $validity ;

        $payload['iat'] = $now->getTimestamp();
        $payload['exp'] = $exp;
        
        }
       
       

        //on base ça sur base 64
        $base64Header = base64_encode(json_encode($header));
        $base64payload = base64_encode(json_encode($payload));

        //on nettoie les valeurs encodées (retrait des + / = )
        $base64Header = str_replace(['+','/','='],['-','_',''],$base64Header);
        $base64payload = str_replace(['+','/','='],['-','_',''],$base64payload);
        
        //on genere la signature 
        $signature = base64_encode($secret);
        $signature = hash_hmac('sha256',$base64Header.'.'.$base64payload,$secret,true);
        $base64signature = base64_encode($signature);
        $base64signature = str_replace(['+','/','='],['-','_',''],$base64signature);

        //on crée le token
        $jwt = $base64Header . '.' . $base64payload . '.' . $base64signature;
        
        return $jwt ;
    }
    //on verifie que le token est valide
    public function isValid(string $token):bool
    {
        return preg_match('/^[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+$/',$token) === 1;
    }
    //ON RECUPÉRE LE PAYLOAD
    public function getPayload(string $token) : array
    {
        //on demonte le token 
        $array = explode('.',$token);
        //on decode le payload 
        $payload = base64_decode($array[1],true);
        //on decode the json string into an array
        $payload = json_decode($payload,true);
        return $payload;
    }
     // ON RECUPERE LE HEADER
     public function getHeader(string $token) : array
     {
         //on demonte le token 
         $array = explode('.',$token);
         //on decode le header 
         $header = base64_decode($array[0],true);
         //on decode the json string into an array
         $header = json_decode($header,true);
         return $header;
     }
    //on verifie si le token a expiré
    public function isExpired(string $token):bool 
    {
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable();
        return $payload['exp'] < $now->getTimestamp();
    }

    //on vverifie la signature de token 
    public function check(string $token , string $secret )
    {
        // on recupere le header et le payload 
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        //on regenre un token 
        $verifToken = $this->generate($header,$payload,$secret , 0);

        return $token === $verifToken;
    }
   
}