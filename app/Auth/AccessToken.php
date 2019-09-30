<?php

namespace App\Auth;

use App\Http\Resources\Auth\UserResource;
use App\User;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;

class AccessToken extends PassportAccessToken
{
    public function convertToJWT(CryptKey $privateKey)
    {
        $builder = new Builder();

        $builder->permittedFor($this->getClient()->getIdentifier())
                ->identifiedBy($this->getIdentifier(), true)
                ->issuedAt(time())
                ->canOnlyBeUsedAfter(time())
                ->expiresAt($this->getExpiryDateTime()->getTimestamp())
                ->relatedTo($this->getUserIdentifier())
                ->withClaim('scopes', $this->getScopes());

        if ($user = User::find($this->getUserIdentifier())) {
            $builder->withClaim('user', new UserResource($user));
        }

        return $builder->getToken(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()));
    }
}
