<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Entity\Alliance;

class AllianceWithMemberCount
{
    public int $memberCount;
    public int $averagePoints = 0;

    public function __construct(private readonly Alliance $alliance, int $memberCount)
    {
        $this->memberCount = $memberCount;
        if ($this->memberCount > 0) {
            $this->averagePoints = (int)floor($this->alliance->getPoints() / $this->memberCount);
        }
    }

    public function getAlliance(): Alliance
    {
        return $this->alliance;
    }

    // Delegate method calls to Alliance
    public function __call(string $method, array $arguments)
    {
        // If method exists on alliance, call it
        if (method_exists($this->alliance, $method)) {
            return $this->alliance->$method(...$arguments);
        }
        
        // Try getter method (e.g., tag() -> getTag())
        $getter = 'get' . ucfirst($method);
        if (method_exists($this->alliance, $getter)) {
            return $this->alliance->$getter(...$arguments);
        }
        
        // Try boolean getter (e.g., publicMemberList() -> isPublicMemberList())
        $isGetter = 'is' . ucfirst($method);
        if (method_exists($this->alliance, $isGetter)) {
            return $this->alliance->$isGetter(...$arguments);
        }
        
        throw new \BadMethodCallException(sprintf('Method %s does not exist on %s', $method, get_class($this->alliance)));
    }
}
