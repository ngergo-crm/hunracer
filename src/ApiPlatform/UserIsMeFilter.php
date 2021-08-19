<?php


namespace App\ApiPlatform;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class UserIsMeFilter implements FilterInterface
{
//    public const ISME_FILTER_CONTEXT = "user_isMe";
    public const ISME_FILTER_CONTEXT = "isMe";

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $isMe = $request->query->get('isMe');
        if(!$isMe) {
            return;
        } else {
            $context[self::ISME_FILTER_CONTEXT] = filter_var($isMe, FILTER_VALIDATE_BOOLEAN);
        }


    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'isMe' => [
                'property' => null,
                'type' => 'bool',
                'required' => false,
                'openapi' => [
                    'description' => 'Returns the current user',
                ],
            ]
        ];
    }
}
