<?php
// file generated with AI assistance: Claude Code - 2026-10-28 07:20:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Tests\Fixtures;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Test stand-in for the za7-core permission voter, which this bundle must
 * not depend on.
 *
 * Mirrors its contract: it votes only on registered permissions (abstains on
 * everything else, roles included), denies without a user, and grants when
 * the token carries at least one role the map assigns to the permission. It
 * never throws and accepts a `null` subject.
 */
final class MapPermissionVoter extends Voter
{
    /**
     * @param list<string>                $registered permissions known to the voter
     * @param array<string, list<string>> $map        permission => roles granting it
     */
    public function __construct(
        private readonly array $registered,
        private readonly array $map,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, $this->registered, true);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        if (null === $token->getUser()) {
            return false;
        }

        return [] !== array_intersect($this->map[$attribute] ?? [], $token->getRoleNames());
    }
}
