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
    private $privateKey;

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return (string)$this->convertToJWT($this->privateKey);
    }

    /**
     * Set the private key used to encrypt this access token.
     *
     * @param \League\OAuth2\Server\CryptKey $privateKey
     */
    public function setPrivateKey(CryptKey $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

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
