<?php 

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter{

    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security ; 
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports(string $attribute, $product): bool 
    {
        return in_array($attribute, [self::EDIT , self::DELETE]) && $product instanceof Products;
    }

    
    protected function VoteOnAttribute($attribute , $product , TokenInterface $token ): bool
    {
        // on récupére l'utilisateur a partir du token
        $user = $token->getUser(); 

        //hadi lazem netfhamha
        if(!$user instanceof UserInterface) return false;

        //on verifie si l'utilisateur est admin 
        if($this->security->isGranted('ROLE_ADMIN'))return true;

        //on verifie les permissions
        switch ($attribute){
            case self::EDIT :
                return $this->canEdit();
                break;
                case self::DELETE : 
                return $this->canDelete();

                break; 
        }
    }

    private function canEdit()
    {
        $this->security->isGranted('ROLE_ADMIN');
    }
    private function canDelete()
    {
        $this->security->isGranted('ROLE_PRODUCT_ADMIN');
        
    }


}